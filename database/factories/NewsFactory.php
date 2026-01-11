<?php

namespace Database\Factories;

use App\Domains\ContentManagement\Models\News;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * Class NewsFactory.
 */
class NewsFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = News::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'title' => $this->faker->sentence,
      'description' => $this->faker->paragraph,
      'url' => urlencode($this->faker->firstName()),
      'created_by' => 3,
      'image' => $this->faker->imageUrl(),
      'enabled' => $this->faker->boolean,
      'link_url' => $this->faker->url,
      'link_caption' => $this->faker->words(3, true),
      'url' => urlencode($this->faker->name),
      'published_at' => now()->subWeek(),
      'tenant_id' => Tenant::defaultId() ?? Tenant::factory(),

    ];
  }

  /**
   * @return NewsFactory
   */
  public function enabled()
  {
    return $this->state(function (array $attributes) {
      return [
        'enabled' => true,
      ];
    });
  }

  /**
   * @return NewsFactory
   */
  public function disabled()
  {
    return $this->state(function (array $attributes) {
      return [
        'enabled' => false,
      ];
    });
  }
}