<?php

namespace Database\Factories;

use App\Domains\Profile\Models\UserProfile;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class UserProfileFactory.
 */
class UserProfileFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = UserProfile::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'email' => $this->faker->unique()->safeEmail,
      'full_name' => $this->faker->name,
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
