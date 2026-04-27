<?php

namespace App\Domains\Profiles\Services;

use App\Domains\Auth\Models\User;
use App\Domains\Profiles\Models\Profile;
use App\Exceptions\GeneralException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileService
{
  public function store(array $data, ?User $actor = null): Profile
  {
    return DB::transaction(function () use ($data, $actor) {
      $data = $this->prepareData($data, $actor);
      $profile = new Profile($data);
      $profile->review_status = Profile::REVIEW_STATUS_APPROVED;
      $profile->created_by = $actor?->id;
      $profile->updated_by = $actor?->id;
      $profile->save();

      $this->syncSharedIdentityFields($profile, $data, $actor);
      $this->refreshUserName($profile);

      return $profile->fresh(['user']);
    });
  }

  public function update(Profile $profile, array $data, ?User $actor = null): Profile
  {
    return DB::transaction(function () use ($profile, $data, $actor) {
      $data = $this->prepareData($data, $actor, $profile);
      $profile->fill($data);
      $profile->updated_by = $actor?->id;
      $profile->save();

      $this->syncSharedIdentityFields($profile, $data, $actor);
      $this->refreshUserName($profile);
      $this->storeCompletenessInSession($profile);

      return $profile->fresh(['user']);
    });
  }

  public function delete(Profile $profile): void
  {
    DB::transaction(function () use ($profile) {
      if ($profile->user_id && Profile::where('user_id', $profile->user_id)->count() <= 1) {
        throw new GeneralException(__('A linked user account must keep at least one profile.'));
      }

      if ($profile->profile_picture) {
        Storage::disk(config('profiles.image.disk'))->delete($profile->profile_picture);
      }

      $profile->delete();
    });
  }

  public function linkExistingProfiles(User $user): Collection
  {
    $profiles = Profile::query()
      ->whereNull('user_id')
      ->whereRaw('LOWER(email) = ?', [mb_strtolower($user->email)])
      ->get();

    if ($profiles->isEmpty()) {
      return collect();
    }

    foreach ($profiles as $profile) {
      $profile->forceFill([
        'user_id' => $user->id,
        'updated_by' => $user->id,
      ])->save();

      activity('profile-link')
        ->performedOn($profile)
        ->causedBy($user)
        ->log('Linked existing profile to newly registered user account.');
    }

    $this->syncRolesFromProfiles($user);
    $this->storeUserCompletenessInSession($user);

    return $profiles;
  }

  public function syncRolesFromProfiles(User $user): void
  {
    $roleNames = Profile::query()
      ->forUser($user)
      ->get()
      ->pluck('type')
      ->map(fn($type) => Profile::TYPE_ROLE_MAP[$type] ?? null)
      ->filter()
      ->values()
      ->all();

    if ($roleNames !== []) {
      $user->syncRoles(array_values(array_unique(array_merge($user->roles->pluck('name')->all(), $roleNames))));
    }
  }

  public function availableProfileTypesForUser(User $user): array
  {
    return collect($user->roles->pluck('name'))
      ->map(fn($roleName) => Profile::ROLE_TYPE_MAP[$roleName] ?? null)
      ->filter()
      ->unique()
      ->values()
      ->all();
  }

  public function storeUserCompletenessInSession(User $user): void
  {
    if (! app()->bound('session')) {
      return;
    }

    $profiles = Profile::query()->forUser($user)->get();

    session([
      'profiles.count' => $profiles->count(),
      'profiles.completeness' => $profiles->mapWithKeys(fn(Profile $profile) => [$profile->id => $profile->calculateCompleteness()])->all(),
      'profiles.has_incomplete' => $profiles->contains(fn(Profile $profile) => $profile->calculateCompleteness() < 100),
      'profiles.available_types' => $this->availableProfileTypesForUser($user),
    ]);
  }

  public function storeCompletenessInSession(Profile $profile): void
  {
    if ($profile->user) {
      $this->storeUserCompletenessInSession($profile->user);
    }
  }

  protected function prepareData(array $data, ?User $actor = null, ?Profile $profile = null): array
  {
    $data['review_status'] = Profile::REVIEW_STATUS_APPROVED;
    $data['email'] = mb_strtolower($data['email']);

    if (isset($data['profile_picture']) && $data['profile_picture'] instanceof UploadedFile) {
      $data['profile_picture'] = $data['profile_picture']->store(
        config('profiles.image.directory'),
        config('profiles.image.disk')
      );

      if ($profile?->profile_picture && $profile->profile_picture !== $data['profile_picture']) {
        Storage::disk(config('profiles.image.disk'))->delete($profile->profile_picture);
      }
    } elseif (array_key_exists('remove_profile_picture', $data) && $data['remove_profile_picture']) {
      if ($profile?->profile_picture) {
        Storage::disk(config('profiles.image.disk'))->delete($profile->profile_picture);
      }

      $data['profile_picture'] = null;
    } else {
      unset($data['profile_picture']);
    }

    if (isset($data['user_id']) && $data['user_id']) {
      $linkedUser = User::find($data['user_id']);
      if ($linkedUser) {
        $data['email'] = mb_strtolower($linkedUser->email);
      }
    } elseif ($actor && empty($data['user_id']) && empty($data['link_to_existing_user'])) {
      $data['user_id'] = $profile?->user_id ?? null;
    }

    unset($data['remove_profile_picture'], $data['link_to_existing_user']);

    return $data;
  }

  protected function syncSharedIdentityFields(Profile $profile, array $data, ?User $actor = null): void
  {
    if (! $profile->user_id) {
      return;
    }

    $fields = array_intersect_key($data, array_flip(Profile::syncedIdentityFields()));

    if ($fields === []) {
      if (! $profile->wasRecentlyCreated) {
        return;
      }

      $seedProfile = Profile::query()
        ->where('user_id', $profile->user_id)
        ->whereKeyNot($profile->id)
        ->first();

      if (! $seedProfile) {
        return;
      }

      $fields = Arr::only($seedProfile->toArray(), Profile::syncedIdentityFields());
      $profile->forceFill($fields)->save();
      return;
    }

    Profile::query()
      ->where('user_id', $profile->user_id)
      ->whereKeyNot($profile->id)
      ->update(array_merge($fields, ['updated_by' => $actor?->id]));
  }

  protected function refreshUserName(Profile $profile): void
  {
    if ($profile->user && $profile->preferred_long_name) {
      $profile->user->forceFill(['name' => $profile->preferred_long_name])->save();
    }
  }
}
