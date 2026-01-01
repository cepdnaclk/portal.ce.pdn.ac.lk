<?php

namespace App\Http\Requests\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class ReorderGalleryImagesRequest extends FormRequest
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
      'ordered_ids' => 'required|array|min:1',
      'ordered_ids.*' => 'required|integer|exists:gallery_images,id',
    ];
  }

  /**
   * Get custom messages for validator errors.
   *
   * @return array
   */
  public function messages()
  {
    return [
      'ordered_ids.required' => 'Please provide the order of images.',
      'ordered_ids.*.exists' => 'One or more image IDs are invalid.',
    ];
  }
}