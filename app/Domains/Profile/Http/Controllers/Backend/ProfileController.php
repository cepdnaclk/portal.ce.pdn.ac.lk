<?php

namespace App\Domains\Profile\Http\Controllers\Backend;

use App\Domains\Profile\Http\Requests\Backend\ProfileRequest;
use App\Domains\Profile\Models\UserProfile;
use App\Domains\Profile\Services\ProfileService;
use App\Http\Controllers\Controller;

/**
 * Class ProfileController.
 */
class ProfileController extends Controller
{
  public function __construct(private ProfileService $profileService) {}

  public function index()
  {
    return view('backend.profiles.index');
  }

  public function create()
  {
    return view('backend.profiles.create');
  }

  public function store(ProfileRequest $request)
  {
    $this->profileService->store($request->profileData());

    return redirect()
      ->route('dashboard.profiles.index')
      ->with('Success', __('Profile created successfully.'));
  }

  public function show(UserProfile $userProfile)
  {
    return view('backend.profiles.show', [
      'profile' => $userProfile->load('profileTypes', 'links', 'user'),
    ]);
  }

  public function edit(UserProfile $userProfile)
  {
    return view('backend.profiles.edit', [
      'profile' => $userProfile->load('profileTypes', 'links'),
    ]);
  }

  public function update(ProfileRequest $request, UserProfile $userProfile)
  {
    $this->profileService->update($userProfile, $request->profileData());

    return redirect()
      ->route('dashboard.profiles.index')
      ->with('Success', __('Profile updated successfully.'));
  }

  public function delete(UserProfile $userProfile)
  {
    return view('backend.profiles.delete', ['profile' => $userProfile]);
  }

  public function destroy(UserProfile $userProfile)
  {
    $this->profileService->delete($userProfile);

    return redirect()
      ->route('dashboard.profiles.index')
      ->with('Success', __('Profile deleted successfully.'));
  }
}
