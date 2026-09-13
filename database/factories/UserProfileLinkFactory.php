<?php

namespace Database\Factories;

use App\Domains\Profile\Models\UserProfile;
use App\Domains\Profile\Models\UserProfileLink;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class UserProfileLinkFactory.
 */
class UserProfileLinkFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = UserProfileLink::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'user_profile_id' => UserProfile::factory(),
      'type' => $this->faker->unique()->randomElement(UserProfileLink::LINK_TYPES),
      'url' => $this->faker->url,
      'created_at' => now(),
      'updated_at' => now(),
    ];
  }
}
