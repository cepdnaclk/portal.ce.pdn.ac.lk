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
        'parent_id'
    ];

    protected $casts = [
        'metadata' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function setMetadataAttribute($value)
    {
        $this->attributes['metadata'] = json_encode($value, JSON_UNESCAPED_SLASHES);
    }

    public function getFormattedMetadataAttribute()
    {
        $response = array();
        if (is_array($this->metadata)) {
            $response = TaxonomyTerm::formatMetadata($this, $this->metadata);
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
        return $this->belongsTo(User::class, 'updated_by');
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
        return $this->hasMany(self::class, 'parent_id')
            ->orderBy('code', 'asc');;
    }

    public function getMetadata($code)
    {
        if (is_array($this->metadata)) {
            foreach ($this->metadata as $item) {
                if ($item['code'] === $code && $item['value'] != null) {
                    return $item['value'];
                }
            }
        }
        return null;
    }

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($taxonomyTerm) {
            $taxonomyTerm->children()->delete();
        });
    }

    public static function getHierarchicalPath($id)
    {
        $term = TaxonomyTerm::find($id);
        if ($term != null) {
            if ($term->parent_id != null) {
                return TaxonomyTerm::getHierarchicalPath($term->parent_id) . " > " . $term->name;
            } else {
                return  $term->name;
            }
        } else {
            return '';
        }
    }

    public static function getByTaxonomy($taxonomy, $parent = null)
    {
        if ($parent == null) {
            $res = TaxonomyTerm::where('taxonomy_id', $taxonomy->id)->whereNull('parent_id');
        } else {
            $res = TaxonomyTerm::where('taxonomy_id', $taxonomy->id)->where('parent_id', $parent);
        }

        $taxonomyTerms = [];
        foreach ($res->get() as $term) {
            $termData = $term->to_dict();

            if ($term->children()->count() > 0) {
                $termData['terms'] = $term->getByTaxonomy($taxonomy, $term->id);
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

    public static function formatMetadata($term, $metadata, $remove_null = True)
    {
        if ($remove_null == False) {
            $filteredMetadata = $metadata;
        } else {
            $filteredMetadata = array_filter($metadata, function ($value) {
                return  !is_null($value['value']);
            });
        }

        $properties = $term->taxonomy->get_properties();
        foreach ($filteredMetadata as $metadata) {
            $code = $metadata['code'];

            if (!array_key_exists($code, $properties)) {
                // If property is deleted, skip it from the response
                continue;
            }

            $taxonomyCode = $properties[$code]['data_type'];
            $metadataValue = $metadata['value'];

            if (($metadataValue != null && $metadataValue !== '') || $remove_null == False) {
                if ($taxonomyCode == 'file') {
                    // Cache file lookup by file ID
                    $fileCacheKey = 'taxonomy_' . (int)$term->taxonomy_id . '_file_' . (int)$metadataValue;
                    $taxonomyFile = cache()->remember($fileCacheKey, 300, function () use ($metadataValue) {
                        return TaxonomyFile::find($metadataValue);
                    });

                    if ($taxonomyFile) {
                        $response[$code] = route(
                            'download.taxonomy-files',
                            ['file_name' => $taxonomyFile->file_name, 'extension' => $taxonomyFile->getFileExtension()]
                        );
                    }
                } elseif ($taxonomyCode == 'datetime') {
                    $timestamp = false;
                    $datetimeCacheKey = 'taxonomy_' . $term->taxonomy_id . '_datetime_' . $metadataValue;

                    // Check if the formatted datetime is already cached
                    $formattedDatetime = cache()->remember($datetimeCacheKey, 300, function () use ($metadataValue, &$timestamp) {
                        // Explicitly treat numeric strings as Unix timestamps
                        if (is_numeric($metadataValue)) {
                            $timestamp = (int)$metadataValue;
                        } else {
                            $timestamp = strtotime($metadataValue);
                        }

                        // Ensure timestamp is valid (not false from strtotime failure, and non-negative)
                        if ($timestamp !== false && $timestamp >= 0) {
                            return date(DATE_ATOM, $timestamp);
                        }

                        return null;
                    });

                    // Add to response if the formatted datetime is not null
                    if (!is_null($formattedDatetime)) {
                        $response[$code] = $formattedDatetime;
                    }
                } else {
                    $response[$code] = $metadataValue;
                }
            }
        }
        return $response;
    }
}