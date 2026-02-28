<?php

namespace App\Domains\Email\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ApiKey extends Model
{
  use HasFactory;
  use Uuid;

  protected $table = 'api_keys';

  protected $fillable = [
    'portal_app_id',
    'key_prefix',
    'key_hash',
    'last_used_at',
    'expires_at',
    'revoked_at',
  ];

  protected $casts = [
    'last_used_at' => 'datetime',
    'expires_at' => 'datetime',
    'revoked_at' => 'datetime',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  protected $keyType = 'string';
  public $incrementing = false;
  protected $uuidName = 'id';

  public function portalApp()
  {
    return $this->belongsTo(PortalApp::class);
  }

  public function deliveryLogs()
  {
    return $this->hasMany(EmailDeliveryLog::class);
  }

  public function isActive(): bool
  {
    if ($this->revoked_at) {
      return false;
    }

    if ($this->expires_at && $this->expires_at->isPast()) {
      return false;
    }

    return $this->portalApp?->status === PortalApp::STATUS_ACTIVE;
  }

  public static function hashKey(string $plain): string
  {
    return hash_hmac('sha256', $plain, config('app.key'));
  }

  public static function issue(PortalApp $portalApp, ?\DateTimeInterface $expiresAt = null): array
  {
    $plain = Str::random(64);
    $hash = self::hashKey($plain);

    $apiKey = self::create([
      'portal_app_id' => $portalApp->id,
      'key_prefix' => substr($plain, 0, 8),
      'key_hash' => $hash,
      'expires_at' => $expiresAt,
    ]);

    return [$apiKey, $plain];
  }
}
