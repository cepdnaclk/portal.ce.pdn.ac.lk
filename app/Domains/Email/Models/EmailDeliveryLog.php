<?php

namespace App\Domains\Email\Models;

use App\Models\Traits\Uuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EmailDeliveryLog extends Model
{
  use HasFactory;
  use Uuid;

  public const STATUS_QUEUED = 'queued';
  public const STATUS_SENT = 'sent';
  public const STATUS_FAILED = 'failed';

  protected $table = 'email_delivery_logs';

  protected $fillable = [
    'portal_app_id',
    'api_key_id',
    'from',
    'to',
    'cc',
    'bcc',
    'subject',
    'template',
    'metadata',
    'provider_message_id',
    'status',
    'failure_reason',
    'sent_at',
  ];

  protected $casts = [
    'to' => 'array',
    'cc' => 'array',
    'bcc' => 'array',
    'metadata' => 'array',
    'sent_at' => 'datetime',
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

  public function apiKey()
  {
    return $this->belongsTo(ApiKey::class);
  }

  public function scopeForPortalApp($query, PortalApp $portalApp)
  {
    return $query->where('portal_app_id', $portalApp->id);
  }
}