<?php

namespace Database\Factories;

use App\Domains\Semester\Models\Semester;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class SemesterFactory.
 */
class SemesterFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Semester::class;

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
     * @return SemesterFactory
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
     * @return SemesterFactory
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
     * @return SemesterFactory
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
     * @return SemesterFactory
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
     * @return SemesterFactory
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
     * @return SemesterFactory
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
     * @return SemesterFactory
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
     * @return SemesterFactory
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
