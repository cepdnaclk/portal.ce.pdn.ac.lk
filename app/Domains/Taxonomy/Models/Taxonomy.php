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
    use HasFactory,
        LogsActivity;


    protected static $logFillable = true;
    protected static $logOnlyDirty = true;

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

    protected $casts = [
        'properties' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user_created()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user_updated()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function terms()
    {
        return $this->hasMany(TaxonomyTerm::class, 'taxonomy_id')
            ->orderBy('parent_id', 'asc')
            ->orderBy('code', 'asc');
    }

    public function first_child_terms()
    {
        return $this->hasMany(TaxonomyTerm::class, 'taxonomy_id')
            ->whereNull('parent_id')
            ->orderBy('code', 'asc');
    }

    public function to_dict()
    {
        $taxonomy = $this->toArray();
        foreach (['properties', 'created_at', 'updated_at', 'created_by', 'updated_by'] as $attribute) {
            unset($taxonomy[$attribute]);
        }
        $taxonomy['properties'] = $this->properties;
        $taxonomy['terms'] = TaxonomyTerm::getByTaxonomy($this->id);
        return $taxonomy;
    }

    protected static function newFactory()
    {
        return TaxonomyFactory::new();
    }
}