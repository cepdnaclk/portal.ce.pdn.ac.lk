<?php

namespace App\Http\Requests\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class UpdateGalleryImageRequest extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize()
  {
    return true; // Authorization handled by middleware
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
  public function rules()
  {
    return [
      'alt_text' => 'nullable|string|max:255',
      'caption' => 'nullable|string|max:1000',
      'credit' => 'nullable|string|max:255',
    ];
  }

  /**
   * Prepare the data for validation.
   *
   * @return void
   */
  protected function prepareForValidation()
  {
    // Strip tags except basic emphasis
    if ($this->has('caption')) {
      $this->merge([
        'caption' => strip_tags($this->caption, '<em><strong><b><i>'),
      ]);
    }

    if ($this->has('alt_text')) {
      $this->merge([
        'alt_text' => strip_tags($this->alt_text),
      ]);
    }

    if ($this->has('credit')) {
      $this->merge([
        'credit' => strip_tags($this->credit),
      ]);
    }
  }
}