<?php

namespace App\Domains\Profile\Models;

use Database\Factories\UserProfileLinkFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Class UserProfileLink.
 */
class UserProfileLink extends Model
{
  use HasFactory;

  public const LINK_TYPES = [
    'linkedin',
    'github',
    'cv',
    'website',
    'google_scholar',
    'researchgate',
    'facebook',
    'twitter',
  ];

  // Display labels, keyed by link type
  public const LINK_TYPE_LABELS = [
    'linkedin' => 'LinkedIn',
    'github' => 'GitHub',
    'cv' => 'CV',
    'website' => 'Website',
    'google_scholar' => 'Google Scholar',
    'researchgate' => 'ResearchGate',
    'facebook' => 'Facebook',
    'twitter' => 'Twitter',
  ];

  protected $fillable = [
    'user_profile_id',
    'type',
    'url',
  ];

  public function profile()
  {
    return $this->belongsTo(UserProfile::class, 'user_profile_id');
  }

  protected static function newFactory()
  {
    return UserProfileLinkFactory::new();
  }
}
