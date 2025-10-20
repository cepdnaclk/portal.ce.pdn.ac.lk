<?php

namespace App\Domains\News\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Gallery\Models\GalleryImage;
use Database\Factories\NewsFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Domains\News\Models\Traits\Scope\NewsScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
     * Get all gallery images for this news item.
     */
    public function gallery(): MorphMany
    {
        return $this->morphMany(GalleryImage::class, 'imageable')->ordered();
    }

    /**
     * Get the cover image for this news item.
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
        return NewsFactory::new();
    }
}