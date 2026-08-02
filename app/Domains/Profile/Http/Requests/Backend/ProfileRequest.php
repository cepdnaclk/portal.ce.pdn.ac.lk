<?php

namespace App\Domains\Profile\Http\Requests\Backend;

use App\Domains\Profile\Models\UserProfileLink;
use App\Domains\Profile\Models\UserProfileType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Class ProfileRequest.
 *
 * Shared by store and update; the unique email rule ignores the bound
 * profile on update. Form shape: scalar fields, links keyed by link type
 * (links[github] = url), types keyed by profile type with an "assigned"
 * flag and per-type attributes.
 */
class ProfileRequest extends FormRequest
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
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'email' => [
        'required',
        'email',
        'max:255',
        Rule::unique('user_profiles', 'email')->ignore($this->route('userProfile')),
      ],
      'alternate_email' => ['nullable', 'email', 'max:255'],
      'full_name' => ['nullable', 'string', 'max:255'],
      'name_with_initials' => ['nullable', 'string', 'max:255'],
      'preferred_short_name' => ['nullable', 'string', 'max:255'],
      'preferred_long_name' => ['nullable', 'string', 'max:255'],
      'honorific' => ['nullable', 'string', 'max:20'],
      'location' => ['nullable', 'string', 'max:255'],
      'current_affiliation' => ['nullable', 'string', 'max:255'],
      'current_position' => ['nullable', 'string', 'max:255'],
      'profile_image' => [
        'nullable',
        'file',
        'mimes:jpeg,jpg',
        'max:' . config('profile.image.max_file_size'),
        'dimensions:min_width=' . config('profile.image.min_width') . ',min_height=' . config('profile.image.min_height'),
      ],
      'links' => ['sometimes', 'array'],
      'links.*' => ['nullable', 'url', 'max:500'],
      'types' => ['sometimes', 'array'],
    ];
  }

  /**
   * Validate link type keys and per-type required attributes.
   *
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

      foreach ((array) $this->input('types', []) as $type => $data) {
        if (! in_array($type, UserProfileType::TYPES, true)) {
          $validator->errors()->add('types', __('Invalid profile type: :type', ['type' => $type]));
          continue;
        }

        if (empty($data['assigned'])) {
          continue;
        }

        $attributes = $data['attributes'] ?? [];

        if ($type === UserProfileType::TYPE_STUDENT) {
          if (! preg_match('#^E/\d{2}/\d{3}$#', $attributes['reg_number'] ?? '')) {
            $validator->errors()->add('types.STUDENT.attributes.reg_number', __('The registration number must match the E/nn/nnn format.'));
          }

          if (empty($attributes['batch'])) {
            $validator->errors()->add('types.STUDENT.attributes.batch', __('The batch is required for the student type.'));
          }
        }

        if ($type === UserProfileType::TYPE_ACADEMIC_STAFF && empty($attributes['designation'])) {
          $validator->errors()->add('types.ACADEMIC_STAFF.attributes.designation', __('The designation is required for the academic staff type.'));
        }
      }
    });
  }

  /**
   * Validated data mapped to the ProfileService shape.
   *
   * @return array
   */
  public function profileData(): array
  {
    $data = $this->validated();
    unset($data['profile_image']);

    $data['links'] = collect($data['links'] ?? [])
      ->filter()
      ->map(fn($url, $type) => ['type' => $type, 'url' => $url])
      ->values()
      ->all();

    $data['types'] = collect($data['types'] ?? [])
      ->filter(fn($type) => ! empty($type['assigned']))
      ->map(function ($type, $name) {
        $attributes = $type['attributes'] ?? [];

        // Comma-separated interests fields become arrays
        foreach (['interests', 'research_interests'] as $key) {
          if (isset($attributes[$key]) && is_string($attributes[$key])) {
            $attributes[$key] = array_values(array_filter(array_map('trim', explode(',', $attributes[$key]))));
          }
        }

        return ['type' => $name, 'attributes' => $attributes];
      })
      ->values()
      ->all();

    return $data;
  }
}
