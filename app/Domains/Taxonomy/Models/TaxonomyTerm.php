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
                if ($item['code'] === $code) {
                    return $item['value'];
                }
            }
        }
        return null;
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

    protected static function newFactory()
    {
        return TaxonomyTermFactory::new();
    }
}