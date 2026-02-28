<?php

namespace App\Domains\Email\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PortalApp extends Model
{
  use HasFactory;
  use Uuid;

  public const STATUS_ACTIVE = 'active';
  public const STATUS_REVOKED = 'revoked';

  protected $table = 'portal_apps';

  protected $fillable = [
    'name',
    'status',
  ];

  protected $casts = [
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  protected $keyType = 'string';
  public $incrementing = false;
  protected $uuidName = 'id';

  public function apiKeys()
  {
    return $this->hasMany(ApiKey::class);
  }

  public function deliveryLogs()
  {
    return $this->hasMany(EmailDeliveryLog::class);
  }

  public function scopeActive($query)
  {
    return $query->where('status', self::STATUS_ACTIVE);
  }
}
