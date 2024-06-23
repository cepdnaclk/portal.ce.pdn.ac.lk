<?php

namespace Database\Factories;

use App\Domains\NewsItem\Models\NewsItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class AnnouncementFactory.
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
            'area' => $this->faker->randomElement(['frontend', 'backend']),
            'type' => $this->faker->randomElement(['info', 'danger', 'warning', 'success']),
            'message' => $this->faker->text,
            'enabled' => $this->faker->boolean,
            'starts_at' => $this->faker->dateTime(),
            'ends_at' => $this->faker->dateTime(),
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

    /**
     * @return NewsItemFactory
     */
    public function frontend()
    {
        return $this->state(function (array $attributes) {
            return [
                'area' => 'frontend',
            ];
        });
    }

    /**
     * @return NewsItemFactory
     */
    public function backend()
    {
        return $this->state(function (array $attributes) {
            return [
                'area' => 'backend',
            ];
        });
    }

    /**
     * @return NewsItemFactory
     */
    public function global()
    {
        return $this->state(function (array $attributes) {
            return [
                'area' => null,
            ];
        });
    }

    /**
     * @return NewsItemFactory
     */
    public function noDates()
    {
        return $this->state(function (array $attributes) {
            return [
                'starts_at' => null,
                'ends_at' => null,
            ];
        });
    }

    /**
     * @return NewsItemFactory
     */
    public function insideDateRange()
    {
        return $this->state(function (array $attributes) {
            return [
                'starts_at' => now()->subWeek(),
                'ends_at' => now()->addWeek(),
            ];
        });
    }

    /**
     * @return NewsItemFactory
     */
    public function outsideDateRange()
    {
        return $this->state(function (array $attributes) {
            return [
                'starts_at' => now()->subWeeks(2),
                'ends_at' => now()->subWeek(),
            ];
        });
    }
}
