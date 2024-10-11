<?php

namespace App\Domains\Taxonomy\Models;

use App\Domains\Auth\Models\User;
use Database\Factories\TaxonomyFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\Taxonomy\Models\Traits\Scope\TaxonomyScope;

/**
 * Class Taxonomy.
 */
class Taxonomy extends Model
{
    use TaxonomyScope,
        HasFactory,
        LogsActivity;


    protected static $logFillable = true;
    protected static $logOnlyDirty = true;

    /**
     * @var string[]
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'properties',
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
        return TaxonomyFactory::new();
    }
}