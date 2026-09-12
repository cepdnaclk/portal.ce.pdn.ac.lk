<?php

namespace App\Domains\Profile\Models;

use App\Domains\Auth\Models\User;
use App\Domains\Profile\Events\ProfileUpdated;
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
  use HasFactory;
  use LogsActivity;
  use SoftDeletes;

  public const HONORIFIC_OPTIONS = [
    'Mr' => 'Mr.',
    'Mrs' => 'Mrs.',
    'Miss' => 'Miss',
    'Ms' => 'Ms.',
    'Dr' => 'Dr.',
    'Prof' => 'Prof.',
    'Rev' => 'Rev.',
  ];

  // Valid values for the STUDENT type's 'department' attribute.
  public const DEPARTMENT_OPTIONS = [
    'Department of Computer Engineering' => 'Department of Computer Engineering',
    'Department of Mechanical Engineering' => 'Department of Mechanical Engineering',
  ];

  /**
   * Map a raw department name from the People API (e.g. "Computer
   * Engineering") to its DEPARTMENT_OPTIONS key (e.g. "Department of
   * Computer Engineering"). Case-insensitive and tolerant of the API value
   * already carrying the "Department of " prefix. Returns null for an
   * unrecognised value so callers can skip it instead of persisting a
   * department that fails Rule::in and can never be selected in the admin
   * dropdown.
   */
  public static function mapDepartment(?string $raw): ?string
  {
    $raw = trim((string) $raw);

    if ($raw === '') {
      return null;
    }

    $normalized = preg_match('/^department of /i', $raw) ? $raw : "Department of $raw";

    foreach (array_keys(self::DEPARTMENT_OPTIONS) as $option) {
      if (strcasecmp($option, $normalized) === 0) {
        return $option;
      }
    }

    return null;
  }

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
    'interests',
    'user_id',
  ];

  protected $dispatchesEvents = [
    'updated' => ProfileUpdated::class,
  ];

  protected $casts = [
    'interests' => 'array',
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

  /**
   * Attributes of an assigned profile type, or [] when the type is not assigned.
   */
  protected function typeAttributes(string $type): array
  {
    return $this->profileTypes->firstWhere('type', $type)?->getAttribute('attributes') ?? [];
  }

  /**
   * Designation by profile type priority: academic staff, then external
   * ("{position}, {affiliation}"), then student.
   */
  public function getDesignationAttribute(): ?string
  {
    $designation = $this->typeAttributes(UserProfileType::TYPE_ACADEMIC_STAFF)['designation'] ?? null;
    if ($designation) {
      return $designation;
    }

    $external = $this->typeAttributes(UserProfileType::TYPE_EXTERNAL);
    $external = implode(', ', array_filter([$external['position'] ?? null, $external['affiliation'] ?? null]));
    if ($external !== '') {
      return $external;
    }

    return $this->profileTypes->contains('type', UserProfileType::TYPE_STUDENT) ? 'Student' : null;
  }

  /**
   * Affiliation by the same priority: the department for academic staff,
   * then the external affiliation, then the student's department.
   */
  public function getAffiliationAttribute(): ?string
  {
    if ($this->profileTypes->contains('type', UserProfileType::TYPE_ACADEMIC_STAFF)) {
      return config('profile.academic_affiliation');
    }

    return ($this->typeAttributes(UserProfileType::TYPE_EXTERNAL)['affiliation'] ?? null)
      ?: ($this->typeAttributes(UserProfileType::TYPE_STUDENT)['department'] ?? null);
  }

  public function isComplete(): bool
  {
    return $this->completeness >= 50;
  }

  public function profileImageUrl(): ?string
  {
    if (! $this->profile_image) {
      return null;
    }

    $path = parse_url($this->profile_image, PHP_URL_PATH);
    $fileName = basename($path ?: '');

    if (preg_match('/^[0-9a-f-]{36}\.jpg$/i', $fileName)) {
      $isStoredFileName = $this->profile_image === $fileName;
      $isLegacyStorageUrl = strpos($path ?: '', '/' . config('profile.image.storage_path') . '/') !== false;

      if ($isStoredFileName || $isLegacyStorageUrl) {
        return route('download.profile-image', ['fileName' => $fileName]);
      }
    }

    return $this->profile_image;
  }

  protected static function newFactory()
  {
    return UserProfileFactory::new();
  }
}
