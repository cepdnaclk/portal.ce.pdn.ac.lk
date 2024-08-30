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
            'title' => $this->faker->sentence(3),
            'version' => $this->faker->numberBetween(1, 10),
            'academic_program' => $this->faker->randomElement(array_keys(Semester::ACADEMIC_PROGRAMS)), // Picks a random element from the predefined academic programs
            'description' => $this->faker->paragraph, 
            'url' => $this->faker->url, 
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'), 
            'updated_at' => now(), 
            'created_by' => \App\Domains\Auth\Models\User::inRandomOrder()->first()->id,
            'updated_by' => \App\Domains\Auth\Models\User::inRandomOrder()->first()->id,
        ];
    }

}