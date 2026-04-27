<?php

namespace Database\Factories;

use App\Domains\Profiles\Models\Profile;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProfileFactory extends Factory
{
  protected $model = Profile::class;

  public function definition()
  {
    return [
      'email' => $this->faker->unique()->safeEmail(),
      'type' => $this->faker->randomElement(Profile::TYPES),
      'full_name' => $this->faker->name(),
      'name_with_initials' => 'J. Doe',
      'preferred_short_name' => 'John',
      'preferred_long_name' => 'John Doe',
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
