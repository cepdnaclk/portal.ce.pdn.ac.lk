<?php

namespace App\Domains\TaxonomyTerms\Models;

use App\Domains\Auth\Models\User;
use Database\Factories\TaxonomyTermsFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\TaxonomyTerms\Models\Traits\Scope\TaxonomyTermsScope;

/**
 * Class TaxonomyTerms.
 */
class TaxonomyTerms extends Model
{
    use TaxonomyTermsScope,
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
        return TaxonomyTermsFactory::new();
    }
}