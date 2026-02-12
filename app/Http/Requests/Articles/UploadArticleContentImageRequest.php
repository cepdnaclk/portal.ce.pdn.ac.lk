<?php

namespace App\Http\Requests\Articles;

use Illuminate\Foundation\Http\FormRequest;

class UploadArticleContentImageRequest extends FormRequest
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
    $maxSize = config('gallery.max_file_size');
    $allowedMimeTypes = config('gallery.allowed_mimes');
    $allowedMimeTypesRule = 'mimetypes:' . implode(',', $allowedMimeTypes);
    return [
      'image' => [
        'required',
        'file',
        $allowedMimeTypesRule,
        'max:' . $maxSize,
      ],
      'tenant_id' => ['required', 'exists:tenants,id'],
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
      if ($this->hasFile('image')) {
        $file = $this->file('image');
        if ($file && $file->isValid()) {
          $imageInfo = getimagesize($file->getRealPath());
          if ($imageInfo) {
            [$width, $height] = $imageInfo;
            $minWidth = config('gallery.min_width');
            $minHeight = config('gallery.min_height');

            if ($width < $minWidth || $height < $minHeight) {
              $validator->errors()->add(
                'image',
                "Image dimensions must be at least {$minWidth}x{$minHeight} pixels."
              );
            }
          }

          $finfo = finfo_open(FILEINFO_MIME_TYPE);
          $mimeType = finfo_file($finfo, $file->getRealPath());
          finfo_close($finfo);

          if (! in_array($mimeType, config('gallery.allowed_mimes'), true)) {
            $validator->errors()->add(
              'image',
              'Invalid file type. Only JPEG images are allowed.'
            );
          }
        }
      }
    });
  }
}
