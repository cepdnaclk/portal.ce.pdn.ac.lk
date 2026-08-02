<?php

namespace App\Domains\Profile\Http\Requests\Frontend;

use App\Domains\Profile\Models\UserProfile;
use App\Domains\Profile\Models\UserProfileLink;
use App\Domains\Profile\Models\UserProfileType;
use Illuminate\Foundation\Http\FormRequest;

/**
 * Class UpdateMyProfileRequest.
 *
 * Self-service profile editing. Email and profile type assignment are
 * deliberately absent from the rules — submitted values are discarded.
 */
class UpdateMyProfileRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true;
  }

  /**
   * User-editable profile field rules, shared with the onboarding wizard.
   *
   * @return array
   */
  public static function profileRules(): array
  {
    return [
      'honorific' => ['nullable', 'string', 'max:20'],
      'full_name' => ['nullable', 'string', 'max:255'],
      'name_with_initials' => ['nullable', 'string', 'max:255'],
      'preferred_short_name' => ['nullable', 'string', 'max:255'],
      'preferred_long_name' => ['nullable', 'string', 'max:255'],
      'location' => ['nullable', 'string', 'max:255'],
      'current_affiliation' => ['nullable', 'string', 'max:255'],
      'current_position' => ['nullable', 'string', 'max:255'],
    ];
  }

  /**
   * Profile picture upload rules.
   *
   * @return array
   */
  public static function profileImageRules(): array
  {
    return [
      'nullable',
      'file',
      'mimes:jpeg,jpg',
      'max:' . config('profile.image.max_file_size'),
      'dimensions:min_width=' . config('profile.image.min_width') . ',min_height=' . config('profile.image.min_height'),
    ];
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return static::profileRules() + [
      'profile_image' => static::profileImageRules(),
      'links' => ['sometimes', 'array'],
      'links.*' => ['nullable', 'url', 'max:500'],
      'types' => ['sometimes', 'array'],
      'types.ACADEMIC_STAFF.attributes.start_date' => ['nullable', 'date'],
      'types.ACADEMIC_STAFF.attributes.end_date' => $this->filled('types.ACADEMIC_STAFF.attributes.start_date')
        ? ['nullable', 'date', 'after_or_equal:types.ACADEMIC_STAFF.attributes.start_date']
        : ['nullable', 'date'],
    ];
  }

  /**
   * @param  \Illuminate\Validation\Validator  $validator
   */
  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      foreach (array_keys((array) $this->input('links', [])) as $linkType) {
        if (! in_array($linkType, UserProfileLink::LINK_TYPES, true)) {
          $validator->errors()->add('links', __('Invalid link type: :type', ['type' => $linkType]));
        }
      }
    });
  }

  /**
   * Validated data mapped to the ProfileService shape, restricted to what
   * the profile owner may change: scalar fields, links, and the attributes
   * of already-assigned types (never the type assignment itself).
   *
   * @return array
   */
  public function profileData(UserProfile $profile): array
  {
    $data = $this->validated();
    unset($data['profile_image']);

    $data['links'] = collect($data['links'] ?? [])
      ->filter()
      ->map(fn($url, $type) => ['type' => $type, 'url' => $url])
      ->values()
      ->all();

    $submitted = $data['types'] ?? [];

    // Keep every assigned type; merge submitted attributes over existing ones.
    // Types submitted but not assigned are ignored.
    $data['types'] = $profile->profileTypes->map(function ($profileType) use ($submitted) {
      $existing = $profileType->getAttribute('attributes') ?? [];
      $incoming = $submitted[$profileType->type]['attributes'] ?? [];

      // Comma-separated interests fields become arrays
      foreach (['interests', 'research_interests'] as $key) {
        if (isset($incoming[$key]) && is_string($incoming[$key])) {
          $incoming[$key] = array_values(array_filter(array_map('trim', explode(',', $incoming[$key]))));
        }
      }

      $attributes = array_merge($existing, $incoming);

      // Students cannot change their sync/admin-owned identifiers
      if ($profileType->type === UserProfileType::TYPE_STUDENT) {
        $attributes['reg_number'] = $existing['reg_number'] ?? null;
        $attributes['batch'] = $existing['batch'] ?? null;
      }

      return ['type' => $profileType->type, 'attributes' => $attributes];
    })->values()->all();

    return $data;
  }
}
