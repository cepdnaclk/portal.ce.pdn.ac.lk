<?php

namespace App\Http\Requests\Profile;

use App\Domains\Profiles\Models\Profile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpsertRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules()
  {
    $profileId = optional($this->route('profile'))->id;
    $maxSize = (int) config('profiles.image.max_size_kb');
    $requiredFields = collect(config('profiles.required_fields', []))->flip();
    $required = fn(string $field) => $requiredFields->has($field) ? ['required'] : ['nullable'];

    return [
      'user_id' => ['nullable', Rule::exists('users', 'id')],
      'email' => [
        'required',
        'email',
        'max:255',
        'regex:/^[^@\s]+@(eng\.pdn\.ac\.lk|ce\.pdn\.ac\.lk)$/i',
        Rule::unique('profiles')->ignore($profileId)->where(fn($query) => $query->where('type', $this->input('type'))),
      ],
      'type' => ['required', Rule::in(Profile::TYPES)],
      'full_name' => array_merge($required('full_name'), ['string', 'max:255']),
      'name_with_initials' => ['nullable', 'string', 'max:255'],
      'preferred_short_name' => ['nullable', 'string', 'max:255'],
      'preferred_long_name' => ['nullable', 'string', 'max:255'],
      'gender' => ['nullable', Rule::in(Profile::GENDERS)],
      'civil_status' => ['nullable', Rule::in(Profile::CIVIL_STATUSES)],
      'honorific' => ['nullable', Rule::in(Profile::HONORIFICS)],
      'reg_no' => ['nullable', 'regex:/^E\/\d{2}\/\d{3}$/'],
      'profile_picture' => ['nullable', 'file', 'mimes:jpg,jpeg', "max:{$maxSize}"],
      'remove_profile_picture' => ['sometimes', 'boolean'],
      'current_position' => array_merge($required('current_position'), ['string', 'max:255']),
      'department' => array_merge($required('department'), [Rule::in(config('profiles.department', []))]),
      'phone_number' => array_merge($required('phone_number'), ['string', 'max:50']),
      'personal_email' => ['nullable', 'email', 'max:255'],
      'office_email' => ['nullable', 'email', 'max:255'],
      'resident_address' => ['nullable', 'string'],
      'current_location' => ['nullable', 'string', 'max:255'],
      'current_affiliation' => ['nullable', 'array'],
      'current_affiliation.affiliation' => ['nullable', 'string', 'max:255'],
      'current_affiliation.start_date' => ['nullable', 'date'],
      'previous_affiliations' => ['nullable', 'array'],
      'previous_affiliations.*.affiliation' => ['required_with:previous_affiliations', 'string', 'max:255'],
      'previous_affiliations.*.start_date' => ['nullable', 'date'],
      'previous_affiliations.*.end_date' => ['nullable', 'date'],
      'biography' => array_merge($required('biography'), ['string']),
      'profile_url' => ['nullable', 'url', 'max:255'],
      'profile_api' => ['nullable', 'url', 'max:255'],
      'profile_website' => ['nullable', 'url', 'max:255'],
      'profile_cv' => ['nullable', 'url', 'max:255'],
      'profile_linkedin' => ['nullable', 'url', 'max:255'],
      'profile_github' => ['nullable', 'url', 'max:255'],
      'profile_researchgate' => ['nullable', 'url', 'max:255'],
      'profile_google_scholar' => ['nullable', 'url', 'max:255'],
      'profile_orcid' => ['nullable', 'url', 'max:255'],
      'profile_facebook' => ['nullable', 'url', 'max:255'],
      'profile_twitter' => ['nullable', 'url', 'max:255'],
      'review_status' => ['nullable', Rule::in([Profile::REVIEW_STATUS_APPROVED])],
    ];
  }

  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      if ($this->hasFile('profile_picture')) {
        $imageInfo = getimagesize($this->file('profile_picture')->getRealPath());

        if ($imageInfo) {
          // TODO Enable later after deciding on aspect ratio requirements
          //
          // [$width, $height] = $imageInfo;
          // $ratio = round($width / max($height, 1), 2);
          // $minRatio = (float) config('profiles.image.min_ratio');
          // $maxRatio = (float) config('profiles.image.max_ratio');

          // if ($ratio < $minRatio || $ratio > $maxRatio) {
          //   $validator->errors()->add('profile_picture', __('Profile picture ratio must be between 1:1 and 3:4.'));
          // }

          $finfo = finfo_open(FILEINFO_MIME_TYPE);
          $mimeType = finfo_file($finfo, $this->file('profile_picture')->getRealPath());
          finfo_close($finfo);

          if (! in_array($mimeType, config('profiles.image.allowed_mimes', []), true)) {
            $validator->errors()->add('profile_picture', __('Only JPG or JPEG profile pictures are allowed.'));
          }
        }
      }

      foreach ((array) $this->input('previous_affiliations', []) as $index => $item) {
        if (! empty($item['start_date']) && ! empty($item['end_date']) && $item['end_date'] < $item['start_date']) {
          $validator->errors()->add("previous_affiliations.{$index}.end_date", __('End date must be after or equal to the start date.'));
        }
      }
    });
  }
}
