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
        return NewsFactory::new();
    }
}
