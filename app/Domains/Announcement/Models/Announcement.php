<?php

namespace App\Domains\Announcement\Models;

use App\Domains\Announcement\Models\Traits\Scope\AnnouncementScope;
use App\Domains\Tenant\Models\Tenant;
use Database\Factories\AnnouncementFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class Announcement.
 */
class Announcement extends Model
{
  use AnnouncementScope,
    HasFactory,
    LogsActivity;

  public const TYPE_FRONTEND = 'frontend';
  public const TYPE_BACKEND = 'backend';
  public const TYPE_BOTH = 'both';

  protected static $logFillable = true;
  protected static $logOnlyDirty = true;

  /**
   * @var string[]
   */
  protected $fillable = [
    'area',
    'type',
    'message',
    'enabled',
    'starts_at',
    'ends_at',
    'tenant_id',
  ];

  /**
   * @var string[]
   */
  protected $dates = [
    'starts_at',
    'ends_at',
  ];

  /**
   * @var string[]
   */
  protected $casts = [
    'enabled' => 'boolean',
  ];

  public function tenant(): BelongsTo
  {
    return $this->belongsTo(Tenant::class);
  }

  public static function areas()
  {
    return [
      self::TYPE_FRONTEND => 'Frontend',
      self::TYPE_BACKEND => 'Backend',
      self::TYPE_BOTH => 'Both',
    ];
  }

  public static function types()
  {
    return [
      'info' => 'Info',
      'danger' => 'Danger',
      'warning' => 'Warning',
      'success' => 'Success'
    ];
  }

  /**
   * Create a new factory instance for the model.
   *
   * @return \Illuminate\Database\Eloquent\Factories\Factory
   */
  protected static function newFactory()
  {
    return AnnouncementFactory::new();
  }
}
