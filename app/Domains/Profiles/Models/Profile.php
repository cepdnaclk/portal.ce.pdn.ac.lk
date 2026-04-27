<?php

namespace App\Domains\Profiles\Models;

use App\Domains\Auth\Models\User;
use Database\Factories\ProfileFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Spatie\Activitylog\Traits\LogsActivity;

class Profile extends Model
{
  use HasFactory, LogsActivity;

  public const TYPE_UNDERGRADUATE_STUDENT = 'UNDERGRADUATE_STUDENT';
  public const TYPE_POSTGRADUATE_STUDENT = 'POSTGRADUATE_STUDENT';
  public const TYPE_ACADEMIC_STAFF = 'ACADEMIC_STAFF';
  public const TYPE_TEMPORARY_ACADEMIC_STAFF = 'TEMPORARY_ACADEMIC_STAFF';
  public const TYPE_ACADEMIC_SUPPORT = 'ACADEMIC_SUPPORT';
  public const TYPE_EXTERNAL = 'EXTERNAL';

  public const REVIEW_STATUS_APPROVED = 'APPROVED';

  public const GENDER_MALE = 'Male';
  public const GENDER_FEMALE = 'Female';

  public const CIVIL_STATUS_SINGLE = 'Single';
  public const CIVIL_STATUS_MARRIED = 'Married';
  public const CIVIL_STATUS_SEPARATED = 'Separated';
  public const CIVIL_STATUS_DIVORCED = 'Divorced';
  public const CIVIL_STATUS_WIDOWED = 'Widowed';

  public const GENDERS = [
    self::GENDER_MALE,
    self::GENDER_FEMALE,
  ];

  public const CIVIL_STATUSES = [
    '',
    self::CIVIL_STATUS_SINGLE,
    self::CIVIL_STATUS_MARRIED,
    self::CIVIL_STATUS_SEPARATED,
    self::CIVIL_STATUS_DIVORCED,
    self::CIVIL_STATUS_WIDOWED,
  ];

  public const HONORIFICS = ['', 'Dr.', 'Prof.', 'Eng.'];

  public const TYPES = [
    self::TYPE_UNDERGRADUATE_STUDENT,
    self::TYPE_POSTGRADUATE_STUDENT,
    self::TYPE_ACADEMIC_STAFF,
    self::TYPE_TEMPORARY_ACADEMIC_STAFF,
    self::TYPE_ACADEMIC_SUPPORT,
    self::TYPE_EXTERNAL,
  ];

  public const TYPE_LABELS = [
    self::TYPE_UNDERGRADUATE_STUDENT => 'Undergraduate Student',
    self::TYPE_POSTGRADUATE_STUDENT => 'Postgraduate Student',
    self::TYPE_ACADEMIC_STAFF => 'Academic Staff',
    self::TYPE_TEMPORARY_ACADEMIC_STAFF => 'Temporary Academic Staff',
    self::TYPE_ACADEMIC_SUPPORT => 'Academic Support',
    self::TYPE_EXTERNAL => 'External',
  ];

  public const ROLE_TYPE_MAP = [
    'Lecturer' => self::TYPE_ACADEMIC_STAFF,
    'Student' => self::TYPE_UNDERGRADUATE_STUDENT,
    'Postgraduate Student' => self::TYPE_POSTGRADUATE_STUDENT,
    'Temporary Academic Staff' => self::TYPE_TEMPORARY_ACADEMIC_STAFF,
    'Academic Support Staff' => self::TYPE_ACADEMIC_SUPPORT,
    'External Collaborator' => self::TYPE_EXTERNAL,
  ];

  public const TYPE_ROLE_MAP = [
    self::TYPE_UNDERGRADUATE_STUDENT => 'Student',
    self::TYPE_POSTGRADUATE_STUDENT => 'Postgraduate Student',
    self::TYPE_ACADEMIC_STAFF => 'Lecturer',
    self::TYPE_TEMPORARY_ACADEMIC_STAFF => 'Temporary Academic Staff',
    self::TYPE_ACADEMIC_SUPPORT => 'Academic Support Staff',
    self::TYPE_EXTERNAL => 'External Collaborator',
  ];

  protected static $logFillable = true;
  protected static $logOnlyDirty = true;

  protected $fillable = [
    'email',
    'user_id',
    'type',
    'full_name',
    'name_with_initials',
    'preferred_short_name',
    'preferred_long_name',
    'gender',
    'civil_status',
    'honorific',
    'reg_no',
    'profile_picture',
    'current_position',
    'department',
    'phone_number',
    'personal_email',
    'office_email',
    'resident_address',
    'current_location',
    'current_affiliation',
    'previous_affiliations',
    'biography',
    'profile_url',
    'profile_api',
    'profile_website',
    'profile_cv',
    'profile_linkedin',
    'profile_github',
    'profile_researchgate',
    'profile_google_scholar',
    'profile_orcid',
    'profile_facebook',
    'profile_twitter',
    'review_status',
    'created_by',
    'updated_by',
  ];

  protected $casts = [
    'current_affiliation' => 'array',
    'previous_affiliations' => 'array',
    'created_at' => 'datetime',
    'updated_at' => 'datetime',
  ];

  protected $appends = [
    'completeness',
    'title',
    'type_label',
  ];

  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function creator(): BelongsTo
  {
    return $this->belongsTo(User::class, 'created_by');
  }

  public function updater(): BelongsTo
  {
    return $this->belongsTo(User::class, 'updated_by');
  }

  public function siblingProfiles(): HasMany
  {
    return $this->hasMany(self::class, 'user_id', 'user_id')->whereKeyNot($this->getKey());
  }

  public function scopeSearch($query, ?string $term)
  {
    if (! $term) {
      return $query;
    }

    return $query->where(function ($inner) use ($term) {
      $term = mb_strtolower($term);
      $inner->whereRaw('LOWER(COALESCE(full_name, \'\')) like ?', ["%{$term}%"])
        ->orWhereRaw('LOWER(COALESCE(preferred_short_name, \'\')) like ?', ["%{$term}%"])
        ->orWhereRaw('LOWER(COALESCE(preferred_long_name, \'\')) like ?', ["%{$term}%"])
        ->orWhereRaw('LOWER(email) like ?', ["%{$term}%"]);
    });
  }

  public function scopeForUser($query, User $user)
  {
    return $query->where('user_id', $user->getKey());
  }

  public function getTypeLabelAttribute(): string
  {
    return Arr::get(self::TYPE_LABELS, $this->type, $this->type);
  }

  public function getProfilePictureUrlAttribute(): ?string
  {
    if (! $this->profile_picture) {
      return null;
    }

    return route('download.profile', ['path' => basename($this->profile_picture)], true);
  }

  public function resolveProfilePictureUrl(): string
  {
    return $this->getProfilePictureUrlAttribute() ?? '/dummy/profile.png';
  }

  public function resolveTitle(): string
  {
    if ($this->honorific !== null && $this->honorific !== '') {
      return $this->honorific;
    }

    if ($this->gender === self::GENDER_MALE) {
      return 'Mr.';
    }

    if ($this->gender === self::GENDER_FEMALE) {
      return match ($this->civil_status) {
        self::CIVIL_STATUS_MARRIED => 'Mrs.',
        self::CIVIL_STATUS_SEPARATED,
        self::CIVIL_STATUS_DIVORCED,
        self::CIVIL_STATUS_WIDOWED => 'Ms.',
        default => 'Miss.',
      };
    }

    return '';
  }

  public function getTitleAttribute(): string
  {
    return $this->resolveTitle();
  }


  public function calculateCompleteness(?array $requiredFields = null): int
  {
    $requiredFields = $requiredFields ?: config('profiles.required_fields', []);
    $requiredFields = array_values(array_filter($requiredFields));

    if ($requiredFields === []) {
      return 100;
    }

    $completed = collect($requiredFields)->filter(function ($field) {
      $value = data_get($this, $field);

      if (is_array($value)) {
        return ! empty(array_filter(Arr::flatten($value), fn($item) => $item !== null && $item !== ''));
      }

      return $value !== null && $value !== '';
    })->count();

    return (int) round(($completed / count($requiredFields)) * 100);
  }

  public function getCompletenessAttribute(): int
  {
    return $this->calculateCompleteness();
  }

  public function accountSummary(): array
  {
    return [
      'user_id' => $this->user?->id,
      'user_name' => $this->user?->name,
      'user_email' => $this->user?->email,
      'has_linked_account' => $this->user_id !== null,
    ];
  }

  public function profile_label(): string
  {
    return ($this->preferred_long_name ?: $this->email) . " ($this->type_label)";
  }

  public static function syncedIdentityFields(): array
  {
    return config('profiles.shared_identity_fields', []);
  }

  protected static function newFactory()
  {
    return ProfileFactory::new();
  }
}