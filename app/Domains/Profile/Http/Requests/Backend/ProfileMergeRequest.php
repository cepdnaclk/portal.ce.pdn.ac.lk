<?php

namespace App\Domains\Profile\Http\Requests\Backend;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileMergeRequest extends FormRequest
{
  public function authorize()
  {
    return true;
  }

  public function rules(): array
  {
    return [
      'profile_ids' => ['required', 'array', 'size:2'],
      'profile_ids.*' => [
        'required',
        'integer',
        'distinct',
        Rule::exists('user_profiles', 'id')->whereNull('deleted_at'),
      ],
      'primary_profile_id' => [
        Rule::requiredIf($this->routeIs('dashboard.profiles.merge.store')),
        'nullable',
        'integer',
      ],
    ];
  }

  public function withValidator($validator): void
  {
    $validator->after(function ($validator) {
      if (
        $this->filled('primary_profile_id')
        && ! in_array((int) $this->input('primary_profile_id'), array_map('intval', (array) $this->input('profile_ids')), true)
      ) {
        $validator->errors()->add('primary_profile_id', __('The primary profile must be one of the selected profiles.'));
      }
    });
  }
}
