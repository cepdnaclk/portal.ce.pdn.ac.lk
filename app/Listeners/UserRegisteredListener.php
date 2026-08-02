<?php

namespace App\Listeners;

use App\Domains\Profiles\Services\ProfileService;
use Illuminate\Auth\Events\Registered;

class UserRegisteredListener
{
  public function __construct(private ProfileService $profileService) {}

  public function handle(Registered $event): void
  {
    $this->profileService->linkExistingProfiles($event->user);
  }
}
