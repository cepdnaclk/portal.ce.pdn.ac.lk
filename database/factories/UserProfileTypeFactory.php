<?php

namespace Database\Factories;

use App\Domains\Profile\Models\UserProfile;
use App\Domains\Profile\Models\UserProfileType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class UserProfileTypeFactory.
 */
class UserProfileTypeFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = UserProfileType::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'user_profile_id' => UserProfile::factory(),
      'type' => UserProfileType::TYPE_STUDENT,
      'attributes' => [
        'reg_number' => 'E/' . $this->faker->numberBetween(10, 25) . '/' . $this->faker->numberBetween(100, 999),
        'batch' => (string) $this->faker->numberBetween(2010, 2026),
      ],
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
