<?php

namespace App\Domains\ContentManagement\Models;

use Database\Factories\EventFactory;
use App\Domains\ContentManagement\Models\Traits\Scope\EventScope;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
use App\Domains\ContentManagement\Models\BaseContent;

/**
 * Class Event.
 */
class Event extends BaseContent
{
  use EventScope;

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
    'tenant_id',
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
   * Create a new factory instance for the model.
   *
   * @return \Illuminate\Database\Eloquent\Factories\Factory
   */
  protected static function newFactory()
  {
    return EventFactory::new();
  }
}