<?php

namespace Database\Factories;

use App\Domains\Profiles\Models\Profile;
use App\Domains\Profiles\Models\ProfileData;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
  protected $model = Profile::class;

  public function configure()
  {
    return $this->afterMaking(function (Profile $profile) {
      if ($profile->profile_data_id) {
        return;
      }

      $profileDataAttributes = $profile->profileData?->getAttributes() ?? [];
      $email = $profileDataAttributes['email'] ?? $this->faker->unique()->safeEmail();

      $profileData = ProfileData::query()
        ->when($profile->user_id, fn($query) => $query->where('user_id', $profile->user_id))
        ->when(! $profile->user_id, fn($query) => $query->where('email', $email))
        ->first() ?: new ProfileData();

      $profileData->fill($profileDataAttributes);
      $profileData->user_id = $profile->user_id;
      $profileData->email = $profileData->email ?: $email;
      $profileData->save();

      $profile->profile_data_id = $profileData->id;
      $profile->user_id = $profile->user_id ?: $profileData->user_id;
    });
  }

  public function definition()
  {
    return [
      'email' => $this->faker->unique()->safeEmail(),
      'type' => $this->faker->randomElement(Profile::TYPES),
      'full_name' => $this->faker->name(),
      'name_with_initials' => null,
      'preferred_short_name' => null,
      'preferred_long_name' => null,
      'gender' => Profile::GENDER_MALE,
      'civil_status' => Profile::CIVIL_STATUS_SINGLE,
      'honorific' => '',
      'reg_no' => 'E/24/001',
      'phone_number' => $this->faker->phoneNumber(),
      'department' => 'Computer Engineering',
      'current_position' => 'Member',
      'review_status' => Profile::REVIEW_STATUS_APPROVED,
      'current_affiliation' => [
        'affiliation' => 'Department of Computer Engineering',
        'start_date' => now()->toDateString(),
      ],
      'previous_affiliations' => [],
    ];
  }
}
