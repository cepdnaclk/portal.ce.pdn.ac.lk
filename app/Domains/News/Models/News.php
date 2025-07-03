<?php

namespace App\Domains\News\Models;

use App\Domains\Auth\Models\User;
use Database\Factories\NewsFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Domains\News\Models\Traits\Scope\NewsScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\LogOptions;

/**
 * Class News.
 */
class News extends Model
{
    use NewsScope,
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
        'description',
        'image',
        'link_url',
        'link_caption',
        'published_at',
    ];

    /**
     * @var string[]
     */
    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function thumbURL()
    {
        if ($this->image != null) return '/img/news/' . $this->image;
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
        return NewsFactory::new();
    }

    /**
     * Get the activity log options for the model.
     *
     * @return \Spatie\Activitylog\LogOptions
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['area', 'type', 'message', 'enabled', 'starts_at', 'ends_at'])
            ->logOnlyDirty();
    }
}