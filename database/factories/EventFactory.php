<?php

namespace Database\Factories;

use App\Domains\Event\Models\Event; 
use Illuminate\Database\Eloquent\Factories\Factory;


/**
 * Class NewsFactory.
 */
class EventFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Event::class;

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
            'image' => $this->faker->imageUrl(),
            'author' => $this->faker->name,
            'enabled' => $this->faker->boolean,
            'link_url' => $this->faker->url,
            'link_caption' => $this->faker->words(3, true),
        ];
    }

    /**
     * @return EventFactory
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
     * @return EventFactory
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