<?php

namespace App\Domains\Profile\Http\Controllers\Frontend;

use App\Domains\Profile\Http\Requests\Frontend\UpdateMyProfileRequest;
use App\Domains\Profile\Models\UserProfile;
use App\Domains\Profile\Services\ProfileService;
use App\Http\Controllers\Controller;

/**
 * Class MyProfileController.
 */
class MyProfileController extends Controller
{
  public function __construct(private ProfileService $profileService) {}

  public function edit()
  {
    return view('frontend.user.profile-manage', [
      'profile' => $this->resolveProfile()->load('profileTypes', 'links'),
    ]);
  }

  public function update(UpdateMyProfileRequest $request)
  {
    $profile = $this->resolveProfile();

    $this->profileService->update($profile, $request->profileData($profile));

    if ($request->hasFile('profile_image')) {
      $this->profileService->replaceProfileImage($profile, $request->file('profile_image'));
    }

    return redirect()
      ->route('intranet.user.profile.manage')
      ->withFlashSuccess(__('Profile updated successfully.'));
  }

  /**
   * Always the logged-in user's own profile — never resolved from the request.
   */
  private function resolveProfile(): UserProfile
  {
    return $this->profileService->findOrCreateForUser(auth()->user());
  }
}
