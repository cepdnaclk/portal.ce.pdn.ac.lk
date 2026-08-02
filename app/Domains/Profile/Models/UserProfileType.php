<?php

namespace App\Domains\Profile\Models;

use Database\Factories\UserProfileTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserProfileType.
 */
class UserProfileType extends Model
{
  use HasFactory;

  public const TYPE_STUDENT = 'STUDENT';
  public const TYPE_ACADEMIC_STAFF = 'ACADEMIC_STAFF';
  public const TYPE_EXTERNAL = 'EXTERNAL';

  public const TYPES = [
    self::TYPE_STUDENT,
    self::TYPE_ACADEMIC_STAFF,
    self::TYPE_EXTERNAL,
  ];

  // Attributes counted by UserProfile completeness and required in requests
  public const REQUIRED_ATTRIBUTES = [
    self::TYPE_STUDENT => ['reg_number', 'batch'],
    self::TYPE_ACADEMIC_STAFF => ['designation'],
    self::TYPE_EXTERNAL => [],
  ];

  protected $fillable = [
    'user_profile_id',
    'type',
    'attributes',
    'source_key',
  ];

  protected $casts = [
    'attributes' => 'array',
  ];

  public function profile()
  {
    return $this->belongsTo(UserProfile::class, 'user_profile_id');
  }

  protected static function newFactory()
  {
    return UserProfileTypeFactory::new();
  }
}
