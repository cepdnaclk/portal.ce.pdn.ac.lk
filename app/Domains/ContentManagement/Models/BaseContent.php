<?php

namespace App\Domains\ContentManagement\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Gallery\Models\GalleryImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Base content model shared by News and Event domains.
 */
abstract class BaseContent extends Model
{
  use HasFactory,
    LogsActivity;


  protected static $logFillable = true;
  protected static $logOnlyDirty = true;

  /**
   * Get thumbnail image URL, with dummy fallback.
   */
  public function thumbURL(): string
  {
    $cover = $this->cover_image;
    if ($cover) {
      return $cover->getSizeUrl('thumb');
    }

    return asset(ltrim(config('gallery.dummy_thumb'), '/'));
  }

  /**
   * Cover image accessor that avoids N+1 queries.
   */
  public function getCoverImageAttribute(): ?GalleryImage
  {
    if ($this->relationLoaded('coverImage')) {
      return $this->getRelationValue('coverImage');
    }

    return $this->coverImage()->first();
  }

  /**
   * Author relationship shared across content models.
   */
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  /**
   * Attached gallery images.
   */
  public function gallery(): MorphMany
  {
    return $this->morphMany(GalleryImage::class, 'imageable')->ordered();
  }

  /**
   * Cover image relation.
   */
  public function coverImage(): MorphOne
  {
    return $this->morphOne(GalleryImage::class, 'imageable')->where('is_cover', true);
  }
}