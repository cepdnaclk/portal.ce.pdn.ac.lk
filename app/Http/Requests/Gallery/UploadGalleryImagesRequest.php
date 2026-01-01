<?php

namespace App\Http\Requests\Gallery;

use Illuminate\Foundation\Http\FormRequest;

class UploadGalleryImagesRequest extends FormRequest
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
    $maxSize = config('gallery.max_file_size');
    $allowedMimes = implode(',', array_map(function ($mime) {
      return str_replace('image/', '', $mime);
    }, config('gallery.allowed_mimes')));

    return [
      'images' => 'required|array|min:1|max:' . config('gallery.max_images'),
      'images.*' => [
        'required',
        'file',
        'mimes:' . $allowedMimes,
        'max:' . $maxSize,
      ],
      'metadata' => 'nullable|array',
      'metadata.*.alt_text' => 'nullable|string|max:255',
      'metadata.*.caption' => 'nullable|string|max:1000',
      'metadata.*.credit' => 'nullable|string|max:255',
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
      'images.required' => 'Please select at least one image to upload.',
      'images.*.mimes' => 'Only JPEG images are allowed.',
      'images.*.max' => 'Each image must not exceed ' . (config('gallery.max_file_size') / 1024) . ' MB.',
    ];
  }

  /**
   * Configure the validator instance.
   *
   * @param  \Illuminate\Validation\Validator  $validator
   * @return void
   */
  public function withValidator($validator)
  {
    $validator->after(function ($validator) {
      // Additional validation for image dimensions
      if ($this->hasFile('images')) {
        foreach ($this->file('images') as $index => $file) {
          if ($file->isValid()) {
            $imageInfo = getimagesize($file->getRealPath());
            if ($imageInfo) {
              [$width, $height] = $imageInfo;
              $minWidth = config('gallery.min_width');
              $minHeight = config('gallery.min_height');

              if ($width < $minWidth || $height < $minHeight) {
                $validator->errors()->add(
                  "images.{$index}",
                  "Image dimensions must be at least {$minWidth}x{$minHeight} pixels."
                );
              }
            }

            // Validate MIME type by content (not just extension)
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $file->getRealPath());
            finfo_close($finfo);

            if (!in_array($mimeType, config('gallery.allowed_mimes'))) {
              $validator->errors()->add(
                "images.{$index}",
                "Invalid file type. Only JPEG images are allowed."
              );
            }
          }
        }
      }
    });
  }
}