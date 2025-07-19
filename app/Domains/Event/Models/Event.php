<?php

namespace App\Domains\Event\Models;

use App\Domains\Auth\Models\User;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Domains\Event\Models\Traits\Scope\EventScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
        $cacheKey = 'event_type_map';
        return cache()->remember(
            $cacheKey,
            now()->addSeconds(self::CACHE_DURATION),
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

    public function thumbURL()
    {
        if ($this->image != null) return '/img/events/' . $this->image;
        else return config('constants.frontend.dummy_thumb');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
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