<?php

namespace App\Domains\Event\Models;

use App\Domains\Event\Models\Traits\Scope\EventScope;
use Database\Factories\EventFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class News.
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
    ];

    public function thumbURL()
    {
        if ($this->image != null) return '/img/events/' . $this->image;
        else return config('constants.frontend.dummy_thumb');
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