<?php

namespace App\Domains\Taxonomy\Models;

use App\Domains\Auth\Models\User;
use Database\Factories\TaxonomyListFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Domains\Tenant\Models\Tenant;

class TaxonomyList extends Model
{
  public const DATA_TYPES = [
    'string',
    'date',
    'url',
    'email',
    'file',
    'page',
  ];

  public const DATA_TYPE_LABELS = [
    'string' => 'String',
    'date' => 'Date',
    'url' => 'URL',
    'email' => 'Email',
    'file' => 'File',
    'page' => 'Page',
  ];

  use HasFactory;
  use LogsActivity;

  protected static $logFillable = true;
  protected static $logOnlyDirty = true;

  protected $fillable = [
    'name',
    'taxonomy_id',
    'data_type',
    'items',
    'tenant_id',
  ];

  protected $casts = [
    'items' => 'array',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  protected $attributes = [
    'items' => '[]',
  ];

  public function taxonomy()
  {
    return $this->belongsTo(Taxonomy::class);
  }

  public function tenant()
  {
    return $this->belongsTo(Tenant::class);
  }

  public function user_created()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function user_updated()
  {
    return $this->belongsTo(User::class, 'updated_by');
  }

  public function setItemsAttribute($value): void
  {
    if (is_string($value)) {
      $value = json_decode($value, true);
    }

    $this->attributes['items'] = json_encode(array_values($value ?? []), JSON_UNESCAPED_SLASHES);
  }

  protected static function newFactory()
  {
    return TaxonomyListFactory::new();
  }
}
