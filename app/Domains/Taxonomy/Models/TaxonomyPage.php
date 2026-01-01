<?php

namespace App\Domains\Taxonomy\Models;

use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Database\Factories\TaxonomyPageFactory;

/**
 * Class TaxonomyPage
 */
class TaxonomyPage extends Model
{
  use HasFactory, LogsActivity;

  protected static $logFillable = true;
  protected static $logOnlyDirty = true;

  protected $fillable = [
    'taxonomy_id',
    'slug',
    'html',
  ];

  protected $casts = [
    'metadata' => 'json',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];


  public function taxonomy()
  {
    return $this->belongsTo(Taxonomy::class);
  }

  public function user_created()
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function user_updated()
  {
    return $this->belongsTo(User::class, 'updated_by');
  }

  protected static function newFactory()
  {
    return TaxonomyPageFactory::new();
  }
}