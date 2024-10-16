<?php

namespace App\Domains\Taxonomy\Models;

use App\Domains\Auth\Models\User;
use Database\Factories\TaxonomyFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
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

    public static $propertyType = [
        'string' => 'String',
        'integer' => 'Integer Number',
        'float' => 'Floating Point Number',
        'date' => 'Date',
        'datetime' => 'Date Time',
        'boolean' => 'Boolean',
        'url' => 'URL',
        'image' => 'Image'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function terms()
    {
        return $this->hasMany(TaxonomyTerm::class, 'taxonomy_id');
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