<?php

namespace App\Domains\Taxonomy\Models;


use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Database\Factories\TaxonomyFileFactory;
use Str;

class TaxonomyFile extends Model
{
    use HasFactory, LogsActivity;

    protected static $logFillable = true;
    protected static $logOnlyDirty = true;

    protected $fillable = [
        'file_name',
        'file_path',
        'taxonomy_id',
    ];


    protected $casts = [
        'metadata' => 'json',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function getFileNameWithExtension(): string
    {
        return pathinfo($this->file_path, PATHINFO_BASENAME);
    }

    public function setFileNameAttribute(string $value): void
    {
        $basename   = pathinfo($value, PATHINFO_FILENAME);
        $extension  = pathinfo($value, PATHINFO_EXTENSION);

        $slugged    = Str::slug($basename);
        $finalName  = $extension
            ? "{$slugged}.{$extension}"
            : $slugged;

        $this->attributes['file_name'] = $finalName;
    }

    public function getFileSize(): string
    {
        $fileSize = $this->metadata['file_size'] ?? null;

        if ($fileSize === null) {
            return "-";
        }

        if ($fileSize >= 1048576) {
            return round($fileSize / 1048576, 2) . ' MB';
        } elseif ($fileSize >= 1024) {
            return round($fileSize / 1024, 2) . ' KB';
        } else {
            return $fileSize . ' bytes';
        }
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
        return $this->belongsTo(Taxonomy::class);
    }

    protected static function newFactory()
    {
        return TaxonomyFileFactory::new();
    }
}