<?php

namespace App\Listeners;

use App\Domains\Auth\Events\User\UserLoggedIn;
use App\Domains\Profiles\Services\ProfileService;

class UserLoginListener
{
  public function __construct(private ProfileService $profileService) {}

  public function handle(UserLoggedIn $event): void
  {
    $this->profileService->storeUserCompletenessInSession($event->user);
  }
}
