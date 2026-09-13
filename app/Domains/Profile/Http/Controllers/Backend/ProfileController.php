<?php

namespace App\Domains\Profile\Http\Controllers\Backend;

use App\Domains\Profile\Http\Requests\Backend\ProfileRequest;
use App\Domains\Profile\Http\Requests\Backend\ProfileMergeRequest;
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
    $profile = $this->profileService->store($request->profileData());

    if ($request->hasFile('profile_image')) {
      $this->profileService->replaceProfileImage($profile, $request->file('profile_image'));
    }

    return redirect()
      ->route('dashboard.profiles.index')
      ->with('Success', __('Profile created successfully.'));
  }

  public function mergeSelect()
  {
    return view('backend.profiles.merge-select', [
      'profileOptions' => UserProfile::query()
        ->select('id', 'email', 'name_with_initials', 'full_name')
        ->orderBy('email')
        ->get()
        ->mapWithKeys(function ($profile) {
          $name = $profile->name_with_initials ?: $profile->full_name;

          return [$profile->id => $name ? "{$profile->email} — {$name}" : $profile->email];
        })
        ->all(),
    ]);
  }

  public function mergeReview(ProfileMergeRequest $request)
  {
    $profiles = UserProfile::query()
      ->with('profileTypes', 'links', 'user')
      ->whereIn('id', $request->validated()['profile_ids'])
      ->get()
      ->sortBy(function ($profile) use ($request) {
        return array_search($profile->id, array_map('intval', $request->input('profile_ids')), true);
      })
      ->values();

    return view('backend.profiles.merge-review', [
      'profiles' => $profiles,
      'hasAccountConflict' => $profiles->pluck('user_id')->filter()->unique()->count() > 1,
    ]);
  }

  public function mergeStore(ProfileMergeRequest $request)
  {
    $data = $request->validated();
    $primary = UserProfile::findOrFail($data['primary_profile_id']);
    $secondaryId = collect($data['profile_ids'])
      ->map(fn($id) => (int) $id)
      ->first(fn($id) => $id !== $primary->id);

    $profile = $this->profileService->merge($primary, UserProfile::findOrFail($secondaryId));

    return redirect()
      ->route('dashboard.profiles.show', $profile)
      ->with('Success', __('Profiles merged successfully.'));
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

    if ($request->hasFile('profile_image')) {
      $this->profileService->replaceProfileImage($userProfile, $request->file('profile_image'));
    }

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
