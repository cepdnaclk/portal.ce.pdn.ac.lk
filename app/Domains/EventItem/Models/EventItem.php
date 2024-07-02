<?php

namespace App\Domains\EventItem\Models;

use App\Domains\EventItem\Models\Traits\Scope\EventItemScope;
use Database\Factories\NewsItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class NewsItem.
 */
class EventItem extends Model
{
    use EventItemScope,
        HasFactory,
        LogsActivity;


    protected static $logFillable = true;
    protected static $logOnlyDirty = true;

    /**
     * @var string[]
     */
    protected $fillable = [
        'title',
        'description',
        'image',
        'enabled',
        'link_url',
        'link_caption',
        'created_at',
        'updated_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'enabled' => 'boolean',
    ];

    public static function types()
    {
        return [
            'info' => 'Info',
            'danger' => 'Danger',
            'warning' => 'Warning',
            'success' => 'Success'
        ];
    }

    /**
     * Create a new factory instance for the model.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    protected static function newFactory()
    {
        return NewsFactory::new();
    }
}