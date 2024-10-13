<?php

namespace App\Domains\Taxonomy\Models;

use App\Domains\Auth\Models\User;
use Database\Factories\TaxonomyTermFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Class TaxonomyTerm.
 */
class TaxonomyTerm extends Model
{
    use HasFactory,
        LogsActivity;


    protected static $logFillable = true;
    protected static $logOnlyDirty = true;

    /**
     * @var string[]
     */
    protected $fillable = [
        'code',
        'name',
        'metadata',
        'taxonomy_id',
        'parent_id',

    ];

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
        return TaxonomyTermFactory::new();
    }
}