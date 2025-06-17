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
        'visibility'
    ];

    public static $propertyType = [
        'string' => 'String',
        'email' => 'Email',
        'integer' => 'Integer Number',
        'float' => 'Floating Point Number',
        'date' => 'Date',
        'datetime' => 'Date Time',
        'boolean' => 'Boolean',
        'url' => 'URL',
        'file' => 'File'
    ];

    protected $casts = [
        'properties' => 'json',
        'visibility' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    //mutator for saving properties
    public function setPropertiesAttribute($value)
    {
        $this->attributes['properties'] = json_encode($value, JSON_UNESCAPED_SLASHES);
    }

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
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function get_properties()
    {
        $result = [];
        foreach ($this->properties as $property) {
            if (isset($property['code']) && isset($property['name']) && isset($property['data_type'])) {
                $result[$property['code']] = [
                    'name' => $property['name'],
                    'data_type' => $property['data_type'],
                ];
            }
        }
        return $result;
    }
    public function terms()
    {
        return $this->hasMany(TaxonomyTerm::class, 'taxonomy_id')
            ->orderBy('parent_id', 'asc')
            ->orderBy('code', 'asc');
    }

    public function files()
    {
        return $this->hasMany(TaxonomyFile::class, 'taxonomy_id')
            ->orderBy('file_name', 'asc')
            ->pluck('file_name', 'id');
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
        $taxonomy['terms'] = TaxonomyTerm::getByTaxonomy($this);
        return $taxonomy;
    }

    protected static function newFactory()
    {
        return TaxonomyFactory::new();
    }
}
