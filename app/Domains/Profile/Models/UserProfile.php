<?php

namespace App\Domains\Profile\Models;

use App\Domains\Auth\Models\User;
use Database\Factories\UserProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * Class UserProfile.
 */
class UserProfile extends Model
{
  use HasFactory,
    LogsActivity,
    SoftDeletes;

  public const HONORIFIC_OPTIONS = [
    'Mr' => 'Mr.',
    'Mrs' => 'Mrs.',
    'Miss' => 'Miss',
    'Ms' => 'Ms.',
    'Dr' => 'Dr.',
    'Prof' => 'Prof.',
    'Rev' => 'Rev.',
  ];

  protected static $logFillable = true;
  protected static $logOnlyDirty = true;

  protected $fillable = [
    'email',
    'alternate_email',
    'full_name',
    'name_with_initials',
    'preferred_short_name',
    'preferred_long_name',
    'honorific',
    'location',
    'current_affiliation',
    'current_position',
    'profile_image',
    'user_id',
  ];

  public function profileTypes()
  {
    return $this->hasMany(UserProfileType::class);
  }

  public function links()
  {
    return $this->hasMany(UserProfileLink::class);
  }

  public function user()
  {
    return $this->belongsTo(User::class);
  }

  /**
   * Match a profile by primary or alternate email.
   */
  public function scopeForEmail($query, string $email)
  {
    return $query->where(function ($query) use ($email) {
      $query->where('email', $email)->orWhere('alternate_email', $email);
    });
  }

  /**
   * Percentage of filled profile fields: the 5 name fields, location,
   * affiliation, position, image, having at least one link, and the
   * required attributes of each assigned profile type.
   */
  public function getCompletenessAttribute(): int
  {
    $checks = [
      $this->full_name,
      $this->name_with_initials,
      $this->preferred_short_name,
      $this->preferred_long_name,
      $this->honorific,
      $this->location,
      $this->current_affiliation,
      $this->current_position,
      $this->profile_image,
      $this->links->isNotEmpty(),
    ];

    foreach ($this->profileTypes as $profileType) {
      // getAttribute: plain ->attributes here would hit Eloquent's internal array
      $typeAttributes = $profileType->getAttribute('attributes') ?? [];

      foreach (UserProfileType::REQUIRED_ATTRIBUTES[$profileType->type] ?? [] as $attribute) {
        $checks[] = ! empty($typeAttributes[$attribute]);
      }
    }

    return (int) round(count(array_filter($checks)) / count($checks) * 100);
  }

  public function isComplete(): bool
  {
    return $this->completeness >= 50;
  }

  protected static function newFactory()
  {
    return UserProfileFactory::new();
  }
}
