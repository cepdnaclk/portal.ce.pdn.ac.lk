<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Profile\Http\Requests\Frontend\UpdateMyProfileRequest;
use App\Domains\Profile\Models\UserProfile;
use App\Domains\Profile\Models\UserProfileLink;
use App\Domains\Profile\Services\ProfileService;
use Illuminate\Support\Arr;
use Livewire\Component;

class ProfileWizard extends Component
{
  public const STEP_FIELDS = [
    1 => ['honorific', 'full_name', 'name_with_initials', 'preferred_short_name', 'preferred_long_name'],
    2 => ['location', 'current_affiliation', 'current_position', 'profile_image'],
  ];

  public int $step = 1;
  public array $fields = [];
  public array $links = [];
  public int $before = 0;
  public ?int $after = null;

  public function mount(): void
  {
    $profile = $this->profile();
    $this->before = $profile->completeness;

    foreach (array_merge(...array_values(self::STEP_FIELDS)) as $field) {
      $this->fields[$field] = $profile->{$field};
    }

    foreach (UserProfileLink::LINK_TYPES as $type) {
      $this->links[$type] = $profile->links->firstWhere('type', $type)?->url;
    }
  }

  protected function rules(): array
  {
    return collect(UpdateMyProfileRequest::profileRules())
      ->mapWithKeys(fn($rule, $field) => ["fields.$field" => $rule])
      ->all() + ['links.*' => ['nullable', 'url', 'max:500']];
  }

  public function next(): void
  {
    $stepFields = array_map(fn($field) => "fields.$field", self::STEP_FIELDS[$this->step]);
    $this->validate(Arr::only($this->rules(), $stepFields));

    $this->step = min(3, $this->step + 1);
  }

  public function back(): void
  {
    $this->step = max(1, $this->step - 1);
  }

  public function finish(): void
  {
    $this->validate();

    $data = array_map(fn($value) => $value === '' ? null : $value, $this->fields);
    $data['links'] = collect($this->links)
      ->filter()
      ->map(fn($url, $type) => ['type' => $type, 'url' => $url])
      ->values()
      ->all();

    $profile = $this->profile();
    app(ProfileService::class)->update($profile, $data);

    $this->after = $profile->refresh()->completeness;
    $this->step = 4; // done screen
  }

  private function profile(): UserProfile
  {
    // Always the logged-in user's own profile
    return auth()->user()->profile ?? app(ProfileService::class)->findOrCreateForUser(auth()->user());
  }

  public function render()
  {
    return view('livewire.backend.profile-wizard', [
      'profileTypes' => auth()->user()->profile?->profileTypes ?? collect(),
    ]);
  }
}