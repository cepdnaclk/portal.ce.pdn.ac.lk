<?php

namespace App\Domains\Taxonomy\Models;


use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Database\Factories\TaxonomyFileFactory;

class TaxonomyFile extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;
    protected static $logOnlyDirty = true;

    protected $fillable = [
        'file_name',
        'file_path',
        'taxonomy_id',
        'metadata',
    ];


    protected $casts = [
        'metadata' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


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
        return $this->belongsTo(Taxonomy::class);
    }

    protected static function newFactory()
    {
        return TaxonomyFileFactory::new();
    }
}