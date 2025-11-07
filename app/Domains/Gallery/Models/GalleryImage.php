<?php

namespace App\Domains\Gallery\Models;

use Database\Factories\GalleryImageFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class GalleryImage.
 */
class GalleryImage extends Model
{
  use SoftDeletes;
  use HasFactory;
  use LogsActivity;

  protected static $logFillable = true;
  protected static $logOnlyDirty = true;

  /**
   * @var string[]
   */
  protected $fillable = [
    'imageable_type',
    'imageable_id',
    'filename',
    'original_filename',
    'disk',
    'path',
    'mime_type',
    'file_size',
    'width',
    'height',
    'alt_text',
    'caption',
    'credit',
    'order',
    'is_cover',
  ];

  /**
   * @var string[]
   */
  protected $casts = [
    'is_cover' => 'boolean',
    'file_size' => 'integer',
    'width' => 'integer',
    'height' => 'integer',
    'order' => 'integer',
  ];

  /**
   * Get the parent imageable model (News or Event).
   */
  public function imageable(): MorphTo
  {
    return $this->morphTo();
  }

  /**
   * Get the URL for the original image.
   *
   * @return string
   */
  public function getUrl(): string
  {
    return route('download.gallery', $this->filename);
  }

  /**
   * Get the URL for a specific image size.
   *
   * @param string $size
   * @return string
   */
  public function getSizeUrl(string $size): string
  {
    return route('download.gallery', $this->getSizePath($size, true));
  }

  /**
   * Get the storage path for a specific image size.
   *
   * @param string $size
   * @return string
   */
  public function getSizePath(string $size, bool $relative = false): string
  {
    $pathInfo = pathinfo($this->path);
    $directory = !$relative ? $pathInfo['dirname'] . '/' : '';
    $filename = $pathInfo['filename'];
    $extension = $pathInfo['extension'];
    return "{$directory}{$filename}_{$size}.{$extension}";
  }

  /**
   * Get all available sizes for this image.
   *
   * @return array
   */
  public function getAllSizes(): array
  {
    $sizes = ['original' => $this->getUrl()];

    foreach (config('gallery.sizes', []) as $sizeName => $config) {
      $sizes[$sizeName] = $this->getSizeUrl($sizeName);
    }

    return $sizes;
  }

  /**
   * Delete all files associated with this image.
   *
   * @return void
   */
  public function deleteFiles(): void
  {
    $disk = Storage::disk($this->disk);

    // Delete original
    if ($disk->exists($this->path)) {
      $disk->delete($this->path);
    }

    // Delete all size variants
    foreach (array_keys(config('gallery.sizes', [])) as $sizeName) {
      $sizePath = $this->getSizePath($sizeName);
      if ($disk->exists($sizePath)) {
        $disk->delete($sizePath);
      }
    }
  }

  /**
   * Scope a query to only include images in order.
   *
   * @param  \Illuminate\Database\Eloquent\Builder  $query
   * @return \Illuminate\Database\Eloquent\Builder
   */
  public function scopeOrdered($query)
  {
    return $query->orderBy('order', 'asc');
  }

  /**
   * Create a new factory instance for the model.
   *
   * @return \Illuminate\Database\Eloquent\Factories\Factory
   */
  protected static function newFactory()
  {
    return GalleryImageFactory::new();
  }

  /**
   * Boot the model.
   */
  protected static function boot()
  {
    parent::boot();

    // When hard deleting, remove all files
    static::deleting(function ($image) {
      if ($image->isForceDeleting()) {
        $image->deleteFiles();
      }
    });
  }
}
