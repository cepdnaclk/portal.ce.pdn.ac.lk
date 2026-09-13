<?php

namespace App\Domains\Profile\Events;

use App\Domains\Profile\Models\UserProfile;
use Illuminate\Queue\SerializesModels;

/**
 * Class ProfileUpdated.
 */
class ProfileUpdated
{
  use SerializesModels;

  /**
   * @var UserProfile
   */
  public $profile;

  public function __construct(UserProfile $profile)
  {
    $this->profile = $profile;
  }
}
