<?php

namespace Database\Factories;

use App\Domains\ContentManagement\Models\Article;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class ArticleFactory.
 */
class ArticleFactory extends Factory
{
  /**
   * The name of the factory's corresponding model.
   *
   * @var string
   */
  protected $model = Article::class;

  /**
   * Define the model's default state.
   *
   * @return array
   */
  public function definition()
  {
    return [
      'title' => $this->faker->sentence,
      'content' => '<p>' . $this->faker->paragraph . '</p>',
      'published_at' => now()->subWeek(),
      'categories_json' => [$this->faker->word, $this->faker->word],
      'gallery_json' => [],
      'content_images_json' => [],
      'enabled' => true,
      'created_by' => 3,
      'updated_by' => null,
      'tenant_id' => Tenant::defaultId() ?? Tenant::factory(),
    ];
  }

  /**
   * @return ArticleFactory
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
   * @return ArticleFactory
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