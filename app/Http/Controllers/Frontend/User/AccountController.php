<?php

namespace App\Http\Controllers\Frontend\User;

use App\Domains\Profiles\Services\ProfileService;

/**
 * Class AccountController.
 */
class AccountController
{
  public function __construct(private ProfileService $profileService) {}

  /**
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
   */
  public function index()
  {
    $user = auth()->user()->load('profiles', 'roles');

    return view('frontend.user.account', [
      'profiles' => $user->profiles,
      'availableProfileTypes' => $this->profileService->availableProfileTypesForUser($user),
      'profileCompleteness' => $user->profileCompleteness(),
    ]);
  }
}
