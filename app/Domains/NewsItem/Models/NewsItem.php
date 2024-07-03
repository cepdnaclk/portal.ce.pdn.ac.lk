<?php

namespace App\Domains\NewsItem\Models;

use App\Domains\NewsItem\Models\Traits\Scope\NewsItemScope;
use Database\Factories\NewsItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class NewsItem.
 */
class NewsItem extends Model
{
    use NewsItemScope,
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
        'author',
        'link_url',
        'link_caption',
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
        return NewsItemFactory::new();
    }
}