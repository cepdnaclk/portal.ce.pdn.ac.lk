<?php

namespace App\Http\Controllers;

use App\Domains\Auth\Models\User;
use App\Domains\Profiles\Models\Profile;
use App\Domains\Profiles\Services\ProfileService;
use App\Http\Requests\Profile\ProfileUpsertRequest;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Jfcherng\Diff\DiffHelper;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Models\Activity;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Http\Controllers\Concerns\StreamsFiles;

class ProfileController extends Controller
{

  use StreamsFiles;

  public function __construct(private ProfileService $profileService) {}

  public function index(Request $request)
  {
    abort_unless($request->user()->can('user.access.profiles.view') || $request->user()->hasAllAccess(), 403);

    return view('profile.admin.index');
  }

  public function myProfiles(Request $request)
  {
    $user = $request->user()->load('profiles', 'roles');
    $availableTypes = $this->profileService->availableProfileTypesForUser($user);

    return view('profile.my.index', [
      'availableTypes' => $availableTypes,
      'existingTypes' => $user->profiles->pluck('type')->all(),
    ]);
  }

  public function create(Request $request)
  {
    abort_unless($request->user()->can('user.access.profiles.edit') || $request->user()->hasAllAccess(), 403);

    $seedProfile = null;
    if ($request->filled('user_id')) {
      $seedProfile = Profile::query()->where('user_id', $request->integer('user_id'))->first();
    }

    return view('profile.admin.create', [
      'profile' => new Profile(array_merge([
        'type' => $request->query('type'),
        'email' => $request->query('email'),
        'user_id' => $request->query('user_id'),
      ], $seedProfile ? \Illuminate\Support\Arr::only($seedProfile->toArray(), Profile::syncedIdentityFields()) : [])),
      'users' => User::orderBy('name')->get(['id', 'name', 'email']),
      'submitRoute' => route('dashboard.profiles.store'),
      'cancelRoute' => route('dashboard.profiles.index'),
      'title' => __('Create Profile'),
      'typeOptions' => Profile::TYPE_LABELS,
      'selfService' => false,
    ]);
  }

  public function createMyProfile(Request $request)
  {
    $user = $request->user()->load('profiles', 'roles');
    $type = $request->query('type');

    if (! in_array($type, $this->profileService->availableProfileTypesForUser($user), true)) {
      abort(403);
    }

    if ($user->profiles->contains('type', $type)) {
      return redirect()->route('dashboard.my-profiles.index')->withFlashInfo(__('That profile already exists.'));
    }

    $seedProfile = $user->profiles->first();

    return view('profile.my.create', [
      'profile' => new Profile(array_merge([
        'type' => $type,
        'email' => $user->email,
        'user_id' => $user->id,
      ], $seedProfile ? \Illuminate\Support\Arr::only($seedProfile->toArray(), Profile::syncedIdentityFields()) : [])),
      'submitRoute' => route('dashboard.my-profiles.store'),
      'cancelRoute' => route('dashboard.my-profiles.index'),
      'title' => __('Create My Profile'),
      'typeOptions' => array_intersect_key(Profile::TYPE_LABELS, array_flip($this->profileService->availableProfileTypesForUser($user))),
      'selfService' => true,
    ]);
  }

  public function store(ProfileUpsertRequest $request)
  {
    abort_unless($request->user()->can('user.access.profiles.edit') || $request->user()->hasAllAccess(), 403);

    $this->profileService->store($request->validated(), $request->user());

    return redirect()->route('dashboard.profiles.index')->withFlashSuccess(__('Profile created successfully.'));
  }

  public function storeMyProfile(ProfileUpsertRequest $request)
  {
    $payload = array_merge($request->validated(), [
      'user_id' => $request->user()->id,
      'email' => $request->user()->email,
    ]);

    if (! in_array($payload['type'], $this->profileService->availableProfileTypesForUser($request->user()), true)) {
      throw ValidationException::withMessages(['type' => __('You are not allowed to create that profile type.')]);
    }

    $this->profileService->store($payload, $request->user());

    return redirect()->route('dashboard.my-profiles.index')->withFlashSuccess(__('Profile created successfully.'));
  }

  public function edit(Request $request, Profile $profile)
  {
    abort_unless($request->user()->can('user.access.profiles.edit') || $request->user()->hasAllAccess(), 403);

    return view('profile.admin.edit', [
      'profile' => $profile->load('user'),
      'users' => User::orderBy('name')->get(['id', 'name', 'email']),
      'submitRoute' => route('dashboard.profiles.update', $profile),
      'cancelRoute' => route('dashboard.profiles.index'),
      'title' => __('Edit Profile'),
      'typeOptions' => Profile::TYPE_LABELS,
      'selfService' => false,
    ]);
  }

  public function delete(Request $request, Profile $profile)
  {
    abort_unless($request->user()->can('user.access.profiles.delete') || $request->user()->hasAllAccess(), 403);

    return view('profile.admin.delete', compact('profile'));
  }

  public function editMyProfile(Request $request, Profile $profile)
  {
    $this->ensureOwnedProfile($request, $profile);

    return view('profile.my.edit', [
      'profile' => $profile,
      'submitRoute' => route('dashboard.my-profiles.update', $profile),
      'cancelRoute' => route('dashboard.my-profiles.index'),
      'title' => __('Edit My Profile'),
      'typeOptions' => array_intersect_key(Profile::TYPE_LABELS, array_flip($this->profileService->availableProfileTypesForUser($request->user()))),
      'selfService' => true,
    ]);
  }

  public function update(ProfileUpsertRequest $request, Profile $profile)
  {
    abort_unless($request->user()->can('user.access.profiles.edit') || $request->user()->hasAllAccess(), 403);

    $this->profileService->update($profile, $request->validated(), $request->user());

    return redirect()->route('dashboard.profiles.index')->withFlashSuccess(__('Profile updated successfully.'));
  }

  public function updateMyProfile(ProfileUpsertRequest $request, Profile $profile)
  {
    $this->ensureOwnedProfile($request, $profile);

    $payload = array_merge($request->validated(), [
      'user_id' => $request->user()->id,
      'email' => $request->user()->email,
      'type' => $profile->type,
    ]);

    $this->profileService->update($profile, $payload, $request->user());

    return redirect()->route('dashboard.my-profiles.index')->withFlashSuccess(__('Profile updated successfully.'));
  }

  public function destroy(Request $request, Profile $profile)
  {
    abort_unless($request->user()->can('user.access.profiles.delete') || $request->user()->hasAllAccess(), 403);

    $this->profileService->delete($profile);

    return redirect()->route('dashboard.profiles.index')->withFlashSuccess(__('Profile deleted successfully.'));
  }

  public function history(Request $request, Profile $profile)
  {
    abort_unless($request->user()->can('user.access.profiles.view') || $request->user()->hasAllAccess(), 403);

    return $this->renderHistoryView('profile.admin.history', $profile);
  }

  public function myHistory(Request $request, Profile $profile)
  {
    $this->ensureOwnedProfile($request, $profile);

    return $this->renderHistoryView('profile.my.history', $profile);
  }

  protected function ensureOwnedProfile(Request $request, Profile $profile): void
  {
    abort_unless((int) $profile->user_id === (int) $request->user()->id, 403);
  }

  protected function renderHistoryView(string $view, Profile $profile)
  {
    $activities = Activity::query()
      ->where('subject_type', Profile::class)
      ->where('subject_id', $profile->id)
      ->with(['causer', 'subject'])
      ->latest()
      ->paginate(15);

    $items = $activities->items();

    foreach ($items as &$activity) {
      $activity = $activity->toArray();
      $diffs = [];
      $old = $activity['properties']['old'] ?? [];
      $new = $activity['properties']['attributes'] ?? [];

      foreach (array_unique(array_merge(array_keys($old), array_keys($new))) as $field) {
        $before = isset($old[$field]) ? $this->normalizeValue($old[$field]) : '';
        $after = isset($new[$field]) ? $this->normalizeValue($new[$field]) : '';

        if ($before === $after) {
          continue;
        }

        $diffs[$field] = DiffHelper::calculate($before, $after, 'SideBySide', ['detailLevel' => 'line']);
      }

      $activity['diffs'] = $diffs;
    }

    return view($view, [
      'profile' => $profile,
      'activities' => $activities->setCollection(collect($items)),
      'diffCss' => DiffHelper::getStyleSheet(),
    ]);
  }

  protected function normalizeValue($value): string
  {
    if (is_array($value)) {
      return json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }

    if (is_bool($value)) {
      return $value ? 'true' : 'false';
    }

    return (string) ($value ?? '');
  }


  public function download(string $path): BinaryFileResponse
  {
    $diskName = config('profiles.image.disk', 'public');
    $disk = Storage::disk($diskName);
    $fileName = basename($path);

    if ($fileName === '') {
      return abort(404, 'File not found.');
    }

    $directory = trim((string) config('profiles.image.directory', 'profiles'), '/');
    $filePath = $directory . '/' . $fileName;

    if (! $disk->exists($filePath)) {
      return abort(404, 'File not found.');
    }

    return $this->streamFile($disk->path($filePath));
  }
}