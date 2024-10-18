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

    protected $fillable = [
        'code',
        'name',
        'metadata',
        'taxonomy_id',
        'parent_id',
    ];

    protected $casts = [
        'metadata' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getFormattedMetadataAttribute()
    {
        $response = array();
        $filteredMetadata = array_filter(json_decode($this->metadata, true), function ($value) {
            return !is_null($value['value']);
        });

        foreach ($filteredMetadata as $metadata) {
            $response[$metadata['code']] = $metadata['value'];
        }

        return $response;
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
        return $this->belongsTo(User::class, 'created_by');
    }

    public function taxonomy()
    {
        return $this->belongsTo(Taxonomy::class, 'taxonomy_id');
    }

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function getMetadata($code)
    {
        $metadata = json_decode($this->metadata, true);

        if (is_array($metadata)) {
            foreach ($metadata as $item) {
                if ($item['code'] === $code && $item['value'] != null) {
                    return $item['value'];
                }
            }
        }
        return null;
    }

    public static function getByTaxonomy($taxonomyId, $parent = null)
    {
        if ($parent == null) {
            $res = TaxonomyTerm::where('taxonomy_id', $taxonomyId)->whereNull('parent_id');
        } else {
            $res = TaxonomyTerm::where('taxonomy_id', $taxonomyId)->where('parent_id', $parent);
        }

        $taxonomyTerms = [];
        foreach ($res->get() as $term) {
            $termData = $term->to_dict();

            if ($term->children()->count() > 0) {
                $termData['terms'] = $term->getByTaxonomy($taxonomyId, $term->id);
            }
            array_push($taxonomyTerms, $termData);
        }
        return $taxonomyTerms;
    }

    public function to_dict()
    {
        $taxonomyTerm = $this->toArray();
        foreach (['id', 'taxonomy_id', 'parent_id', 'metadata', 'created_at', 'updated_at', 'created_by', 'updated_by'] as $attribute) {
            unset($taxonomyTerm[$attribute]);
        }
        $taxonomyTerm['metadata'] = $this->formatted_metadata;
        return $taxonomyTerm;
    }

    protected static function newFactory()
    {
        return TaxonomyTermFactory::new();
    }
}