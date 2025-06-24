<?php

namespace App\Domains\Taxonomy\Models;

use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Database\Factories\TaxonomyPageFactory;

/**
 * Class TaxonomyPage
 */
class TaxonomyPage extends Model
{
    use HasFactory, LogsActivity;

    /** @var string[] */
    protected $fillable = [
        'taxonomy_id',
        'slug',
        'html',
    ];

    /** @var array<string,string> */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function booted(): void
    {
        static::saved(function (self $page): void {
            Storage::disk('public')->put("taxonomy-pages/{$page->slug}.html", $page->html);
        });

        static::deleted(function (self $page): void {
            Storage::disk('public')->delete("taxonomy-pages/{$page->slug}.html");
        });
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo */
    public function taxonomy()
    {
        return $this->belongsTo(Taxonomy::class);
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo */
    public function user_created()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /** @return \Illuminate\Database\Eloquent\Relations\BelongsTo */
    public function user_updated()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return TaxonomyPageFactory::new();
    }
}
