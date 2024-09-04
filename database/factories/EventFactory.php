<?php

namespace Database\Factories;

use App\Domains\Event\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;


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
            'url' => urlencode($this->faker->firstName()),
            'image' => $this->faker->imageUrl(),
            'created_by' => 4,
            'enabled' => $this->faker->boolean,
            'link_url' => $this->faker->url,
            'link_caption' => $this->faker->words(3, true),
            'published_at' => Carbon::now()->subWeek()->format('Y-m-d'),
            'start_at' => Carbon::now()->subWeek()->format('Y-m-d\TH:i'),
            'end_at' => Carbon::now()->subDay()->format('Y-m-d\TH:i'),
            'location' => $this->faker->company,
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