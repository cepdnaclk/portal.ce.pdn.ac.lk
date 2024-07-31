<?php

namespace App\Domains\News\Models;

use App\Domains\News\Models\Traits\Scope\NewsScope;
use Database\Factories\NewsFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

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
        'description',
        'image',
        'link_url',
        'link_caption',
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
        return NewsFactory::new();
    }
}