<?php

namespace App\Domains\Profiles\Models;

use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;

class ProfileData extends Model
{
  use LogsActivity;

  protected static $logFillable = true;
  protected static $logOnlyDirty = true;

  protected $table = 'profile_data';

  protected $fillable = [
    'email',
    'user_id',
    'full_name',
    'name_with_initials',
    'preferred_short_name',
    'preferred_long_name',
    'gender',
    'civil_status',
    'honorific',
    'profile_picture',
    'phone_number',
    'personal_email',
    'office_email',
    'resident_address',
    'current_location',
    'current_affiliation',
    'previous_affiliations',
    'biography',
    'profile_website',
    'profile_cv',
    'profile_linkedin',
    'profile_github',
    'profile_researchgate',
    'profile_google_scholar',
    'profile_orcid',
    'profile_facebook',
    'profile_twitter',
  ];

  protected $casts = [
    'current_affiliation' => 'array',
    'previous_affiliations' => 'array',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function userProfiles(): HasMany
  {
    return $this->hasMany(UserProfile::class);
  }

  public static function fields(): array
  {
    return (new static())->getFillable();
  }
}
