<?php

namespace Database\Factories;

use App\Domains\NewsItem\Models\NewsItem;
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * Class NewsFactory.
 */
class NewsItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = NewsItem::class;

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
            'author' => $this->faker->name,
            'image' => $this->faker->imageUrl(),
            'enabled' => $this->faker->boolean,
            'link_url' => $this->faker->url,
            'link_caption' => $this->faker->words(3, true),
        ];
    }

    /**
     * @return NewsItemFactory
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
     * @return NewsItemFactory
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


