<?php

namespace App\Domains\Event\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Gallery\Models\GalleryImage;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Domains\Event\Models\Traits\Scope\EventScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Domains\Taxonomy\Models\TaxonomyTerm;

/**
 * Class Event.
 */
class Event extends Model
{
  use EventScope,
    HasFactory,
    LogsActivity;


  protected static $logFillable = true;
  protected static $logOnlyDirty = true;

  /**
   * @var string[]
   */
  protected $fillable = [
    'title',
    'url',
    'event_type',
    'published_at',
    'description',
    'image',
    'enabled',
    'link_url',
    'link_caption',
    'start_at',
    'end_at',
    'location',
    'created_at',
    'updated_at',
  ];

  /**
   * @var string[]
   */
  protected $casts = [
    'enabled' => 'boolean',
    'event_type' => 'array',
  ];


  const CACHE_DURATION = 3600; // Cache duration in seconds (1 hour)

  public static function eventTypeMap(): array
  {
    return cache()->remember(
      'event_type_map',
      self::CACHE_DURATION,
      function () {
        $events = TaxonomyTerm::where('code', 'events')->firstOrFail();
        $eventList = [];
        foreach ($events->children as $event) {
          $code = (int) $event->getFormattedMetadata('key');
          $eventList[$code] = $event->name;
        }
        return $eventList;
      }
    );
  }

  /**
   * Get the URL of the thumbnail image for this news item.
   *
   * @return string
   */
  public function thumbURL()
  {
    if ($cover = $this->coverImage()->first()) {
      return $cover->getSizeUrl('thumb');
    }
    return asset(ltrim(config('gallery.dummy_thumb'), '/'));
  }

  /**
   * Get the user that created this news item.
   *
   * @return User
   */
  public function user()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  /**
   * Get all gallery images for this event.
   */
  public function gallery(): MorphMany
  {
    return $this->morphMany(GalleryImage::class, 'imageable')->ordered();
  }

  /**
   * Get the cover image for this event.
   */
  public function coverImage()
  {
    return $this->morphOne(GalleryImage::class, 'imageable')->where('is_cover', true);
  }

  /**
   * Create a new factory instance for the model.
   *
   * @return \Illuminate\Database\Eloquent\Factories\Factory
   */
  protected static function newFactory()
  {
    return EventFactory::new();
  }
}
