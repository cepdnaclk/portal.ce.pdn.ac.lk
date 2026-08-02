<?php

namespace App\Domains\Profile\Services;

use App\Domains\Auth\Models\User;
use App\Domains\Profile\Models\UserProfile;
use App\Domains\Profile\Models\UserProfileType;
use App\Exceptions\GeneralException;
use App\Services\BaseService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

/**
 * Class ProfileService.
 */
class ProfileService extends BaseService
{
  /**
   * ProfileService constructor.
   *
   * @param  UserProfile  $profile
   */
  public function __construct(UserProfile $profile, private ProfileImageService $profileImageService)
  {
    $this->model = $profile;
  }

  /**
   * Create a profile with optional types and links.
   *
   * $data: fillable scalars + 'types' => [['type' => ..., 'attributes' => [...]], ...]
   * and 'links' => [['type' => ..., 'url' => ...], ...]
   *
   * @throws GeneralException
   */
  public function store(array $data): UserProfile
  {
    if ($this->model::withTrashed()->where('email', $data['email'] ?? null)->exists()) {
      throw new GeneralException(__('A profile already exists for this email address.'));
    }

    return DB::transaction(function () use ($data) {
      $profile = $this->model::create(Arr::only($data, $this->model->getFillable()));

      foreach ($data['types'] ?? [] as $type) {
        $this->addType($profile, $type['type'], $type['attributes'] ?? []);
      }

      foreach ($data['links'] ?? [] as $link) {
        $profile->links()->create(Arr::only($link, ['type', 'url']));
      }

      return $profile->refresh();
    });
  }

  /**
   * Update scalar fields, and sync links/types when the keys are present.
   * Email only changes when explicitly passed (admin flow).
   */
  public function update(UserProfile $profile, array $data): UserProfile
  {
    return DB::transaction(function () use ($profile, $data) {
      $profile->update(Arr::only($data, $this->model->getFillable()));

      if (array_key_exists('links', $data)) {
        $profile->links()->whereNotIn('type', Arr::pluck($data['links'], 'type'))->delete();

        foreach ($data['links'] as $link) {
          $profile->links()->updateOrCreate(['type' => $link['type']], ['url' => $link['url']]);
        }
      }

      if (array_key_exists('types', $data)) {
        $profile->profileTypes()->whereNotIn('type', Arr::pluck($data['types'], 'type'))->delete();

        foreach ($data['types'] as $type) {
          $this->addType($profile, $type['type'], $type['attributes'] ?? []);
        }
      }

      return $profile->refresh();
    });
  }

  public function delete(UserProfile $profile): void
  {
    $this->profileImageService->delete($profile);
    $profile->delete();
  }

  /**
   * Merge a secondary profile into the selected primary profile.
   * Populated primary values win; missing values are filled from secondary.
   *
   * @throws GeneralException
   */
  public function merge(UserProfile $primary, UserProfile $secondary): UserProfile
  {
    if ($primary->is($secondary)) {
      throw new GeneralException(__('Select two different profiles to merge.'));
    }

    [$profile, $discardedImage] = DB::transaction(function () use ($primary, $secondary) {
      $profiles = $this->model::query()
        ->whereIn('id', [$primary->id, $secondary->id])
        ->lockForUpdate()
        ->get()
        ->keyBy('id');
      $primary = $profiles->get($primary->id);
      $secondary = $profiles->get($secondary->id);

      if (! $primary || ! $secondary) {
        throw new GeneralException(__('One of the selected profiles is no longer available.'));
      }

      if ($primary->user_id && $secondary->user_id && $primary->user_id !== $secondary->user_id) {
        throw new GeneralException(__('Profiles linked to different portal accounts cannot be merged.'));
      }

      $secondaryImage = $secondary->profile_image;
      $values = [];

      foreach (
        [
          'full_name',
          'name_with_initials',
          'preferred_short_name',
          'preferred_long_name',
          'honorific',
          'location',
          'current_affiliation',
          'current_position',
          'profile_image',
          'user_id',
        ] as $field
      ) {
        $values[$field] = filled($primary->{$field}) ? $primary->{$field} : $secondary->{$field};
      }

      $values['alternate_email'] = $this->mergedAlternateEmail($primary, $secondary);
      $primary->update($values);

      foreach ($secondary->profileTypes()->get() as $secondaryType) {
        $primaryType = $primary->profileTypes()->where('type', $secondaryType->type)->first();

        if (! $primaryType) {
          $secondaryType->update(['user_profile_id' => $primary->id]);
          continue;
        }

        $attributes = $this->mergeFilledValues(
          $primaryType->getAttribute('attributes') ?? [],
          $secondaryType->getAttribute('attributes') ?? []
        );
        $sourceKey = filled($primaryType->source_key) ? $primaryType->source_key : $secondaryType->source_key;

        // Release the secondary unique (type, source_key) before updating primary.
        $secondaryType->delete();
        $primaryType->update(['attributes' => $attributes, 'source_key' => $sourceKey]);
      }

      foreach ($secondary->links()->get() as $secondaryLink) {
        $primaryLink = $primary->links()->where('type', $secondaryLink->type)->first();

        if ($primaryLink) {
          $secondaryLink->delete();
        } else {
          $secondaryLink->update(['user_profile_id' => $primary->id]);
        }
      }

      $secondary->delete();
      $discardedImage = filled($secondaryImage) && $secondaryImage !== $primary->profile_image
        ? $secondaryImage
        : null;

      return [$primary->refresh(), $discardedImage];
    });

    if ($discardedImage) {
      $discardedProfile = new UserProfile(['profile_image' => $discardedImage]);
      $this->profileImageService->delete($discardedProfile);
    }

    return $profile->load('profileTypes', 'links', 'user');
  }

  /**
   * Replace the profile picture and remove the previous uploaded file.
   */
  public function replaceProfileImage(UserProfile $profile, UploadedFile $file): UserProfile
  {
    return $this->profileImageService->replace($profile, $file);
  }

  /**
   * @throws GeneralException
   */
  public function addType(UserProfile $profile, string $type, array $attributes = []): UserProfileType
  {
    if (! in_array($type, UserProfileType::TYPES, true)) {
      throw new GeneralException(__('Invalid profile type: :type', ['type' => $type]));
    }

    return DB::transaction(function () use ($profile, $type, $attributes) {
      return $profile->profileTypes()->updateOrCreate(
        ['type' => $type],
        ['attributes' => $attributes]
      );
    });
  }

  public function removeType(UserProfile $profile, string $type): void
  {
    $profile->profileTypes()->where('type', $type)->delete();
  }

  private function mergedAlternateEmail(UserProfile $primary, UserProfile $secondary): ?string
  {
    if (filled($primary->alternate_email)) {
      return $primary->alternate_email;
    }

    foreach ([$secondary->email, $secondary->alternate_email] as $email) {
      if (filled($email) && strcasecmp($email, $primary->email) !== 0) {
        return $email;
      }
    }

    return null;
  }

  private function mergeFilledValues(array $primary, array $secondary): array
  {
    foreach ($primary as $key => $value) {
      if (filled($value) || ! array_key_exists($key, $secondary)) {
        $secondary[$key] = $value;
      }
    }

    return $secondary;
  }

  /**
   * Find the profile for a user's email and link it, or create a minimal one.
   * Idempotent: a user with a linked profile gets it back unchanged.
   */
  public function findOrCreateForUser(User $user): ?UserProfile
  {
    if ($user->profile) {
      return $user->profile;
    }

    if (! $user->email) {
      return null;
    }

    return DB::transaction(function () use ($user) {
      $profile = $this->model::forEmail($user->email)->first();

      if ($profile) {
        if ($profile->user_id === null) {
          $profile->update(['user_id' => $user->id]);
        }

        return $profile;
      }

      return $this->model::create([
        'email' => $user->email,
        'full_name' => $user->name,
        'user_id' => $user->id,
      ]);
    });
  }
}
