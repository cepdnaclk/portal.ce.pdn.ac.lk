<?php

namespace App\Domains\Gallery\Services;

use App\Domains\Gallery\Models\GalleryImage;
use App\Domains\Gallery\Jobs\ProcessGalleryImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class GalleryService
{
  /**
   * Upload and attach images to an imageable model (News or Event).
   *
   * @param mixed $imageable
   * @param array|UploadedFile $files
   * @param array $metadata
   * @return array
   */
  public function uploadImages($imageable, $files, array $metadata = []): array
  {
    $files = is_array($files) ? $files : [$files];
    $uploadedImages = [];

    DB::beginTransaction();

    try {
      foreach ($files as $index => $file) {
        $fileMetadata = $metadata[$index] ?? [];
        $image = $this->uploadSingleImage($imageable, $file, $fileMetadata);
        $uploadedImages[] = $image;
      }

      DB::commit();

      // Process images (resize) after transaction
      foreach ($uploadedImages as $image) {
        $this->processImage($image);
      }

      return $uploadedImages;
    } catch (\Exception $e) {
      DB::rollBack();

      // Clean up any uploaded files
      foreach ($uploadedImages as $image) {
        $image->deleteFiles();
        $image->forceDelete();
      }

      throw $e;
    }
  }

  /**
   * Upload a single image.
   *
   * @param mixed $imageable
   * @param UploadedFile $file
   * @param array $metadata
   * @return GalleryImage
   */
  protected function uploadSingleImage($imageable, UploadedFile $file, array $metadata = []): GalleryImage
  {
    // Validate image dimensions
    $imageInfo = getimagesize($file->getRealPath());
    if (!$imageInfo) {
      throw new \Exception('Invalid image file');
    }

    [$width, $height] = $imageInfo;

    if ($width < config('gallery.min_width') || $height < config('gallery.min_height')) {
      throw new \Exception("Image dimensions must be at least " .
        config('gallery.min_width') . "x" . config('gallery.min_height') . " pixels");
    }

    // Generate unique filename
    $disk = config('gallery.disk');
    $extension = $file->getClientOriginalExtension();
    $filename = Str::random(10) . '.' . $extension;
    $basePath = config('gallery.storage_path');
    $path = "{$basePath}/{$filename}";

    // Store original file
    Storage::disk($disk)->put($path, file_get_contents($file->getRealPath()));

    // Get next order value
    $nextOrder = $imageable->gallery()->max('order') + 1;

    // Create database record
    $galleryImage = new GalleryImage([
      'filename' => $filename,
      'original_filename' => $file->getClientOriginalName(),
      'disk' => $disk,
      'path' => $path,
      'mime_type' => $file->getMimeType(),
      'file_size' => $file->getSize(),
      'width' => $width,
      'height' => $height,
      'alt_text' => $metadata['alt_text'] ?? null,
      'caption' => $metadata['caption'] ?? null,
      'credit' => $metadata['credit'] ?? null,
      'order' => $nextOrder,
      'is_cover' => false,
    ]);

    $imageable->gallery()->save($galleryImage);

    // If this is the first image, make it the cover
    if ($imageable->gallery()->count() === 1) {
      $this->setCoverImage($imageable, $galleryImage->id);
    }

    return $galleryImage;
  }

  /**
   * Process image to generate different sizes.
   *
   * @param GalleryImage $galleryImage
   * @return void
   */
  protected function processImage(GalleryImage $galleryImage): void
  {
    if (config('gallery.queue_processing')) {
      ProcessGalleryImage::dispatch($galleryImage);
    } else {
      $this->generateImageSizes($galleryImage);
    }
  }

  /**
   * Generate different image sizes.
   *
   * @param GalleryImage $galleryImage
   * @return void
   */
  public function generateImageSizes(GalleryImage $galleryImage): void
  {
    $disk = Storage::disk($galleryImage->disk);
    $originalPath = $galleryImage->path;

    if (!$disk->exists($originalPath)) {
      return;
    }

    $originalContent = $disk->get($originalPath);
    $image = Image::make($originalContent);

    foreach (config('gallery.sizes', []) as $sizeName => $config) {
      $width = $config['width'];
      $height = $config['height'];
      $aspectRatio = $config['aspect_ratio'] ?? true;

      $resizedImage = clone $image;

      if ($aspectRatio) {
        $resizedImage->resize($width, $height, function ($constraint) {
          $constraint->aspectRatio();
          $constraint->upsize();
        });
      } else {
        $resizedImage->fit($width, $height);
      }

      $sizePath = $galleryImage->getSizePath($sizeName);
      $disk->put($sizePath, (string) $resizedImage->encode());
    }
  }

  /**
   * Update image metadata.
   *
   * @param GalleryImage $image
   * @param array $data
   * @return GalleryImage
   */
  public function updateImage(GalleryImage $image, array $data): GalleryImage
  {
    $image->update([
      'alt_text' => $data['alt_text'] ?? $image->alt_text,
      'caption' => $data['caption'] ?? $image->caption,
      'credit' => $data['credit'] ?? $image->credit,
    ]);

    return $image->fresh();
  }

  /**
   * Reorder gallery images.
   *
   * @param mixed $imageable
   * @param array $orderedIds
   * @return void
   */
  public function reorderImages($imageable, array $orderedIds): void
  {
    DB::transaction(function () use ($imageable, $orderedIds) {
      foreach ($orderedIds as $index => $imageId) {
        $imageable->gallery()
          ->where('id', $imageId)
          ->update(['order' => $index]);
      }
    });
  }

  /**
   * Set an image as the cover image.
   *
   * @param mixed $imageable
   * @param int $imageId
   * @return GalleryImage
   */
  public function setCoverImage($imageable, int $imageId): GalleryImage
  {
    DB::transaction(function () use ($imageable, $imageId) {
      // Remove cover status from all images
      $imageable->gallery()->update(['is_cover' => false]);

      // Set new cover
      $imageable->gallery()->where('id', $imageId)->update(['is_cover' => true]);
    });

    return GalleryImage::find($imageId);
  }

  /**
   * Delete an image.
   *
   * @param GalleryImage $image
   * @return void
   */
  public function deleteImage(GalleryImage $image): void
  {
    $imageable = $image->imageable;
    $wasCover = $image->is_cover;

    // Soft delete
    $image->delete();

    // If this was the cover, promote another image
    if ($wasCover) {
      $newCover = $imageable->gallery()->first();
      if ($newCover) {
        $this->setCoverImage($imageable, $newCover->id);
      }
    }
  }

  /**
   * Get gallery statistics for an imageable.
   *
   * @param mixed $imageable
   * @return array
   */
  public function getGalleryStats($imageable): array
  {
    $gallery = $imageable->gallery;

    return [
      'total_images' => $gallery->count(),
      'total_size' => $gallery->sum('file_size'),
      'max_images' => config('gallery.max_images'),
      'can_add_more' => $gallery->count() < config('gallery.max_images'),
    ];
  }

  /**
   * Permanently delete all gallery images attached to the given model.
   *
   * @param mixed $imageable
   * @return void
   */
  public function deleteGalleryForImageable($imageable): void
  {
    if (!$imageable) {
      return;
    }

    DB::transaction(function () use ($imageable) {
      $query = $imageable->gallery()->lockForUpdate();

      foreach ($query->cursor() as $image) {
        $image->forceDelete();
      }
    });
  }
}