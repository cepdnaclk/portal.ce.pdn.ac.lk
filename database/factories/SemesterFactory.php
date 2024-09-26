<?php

namespace Database\Factories;

use App\Domains\Auth\Models\User;
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
            'version' => $this->faker->randomElement(array_keys(Semester::getVersions())),
            'academic_program' => $this->faker->randomElement(array_keys(Semester::getAcademicPrograms())),
            'description' => $this->faker->paragraph,
            'url' => $this->faker->url,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
            'created_by' => User::inRandomOrder()->first()->id,
            'updated_by' => User::inRandomOrder()->first()->id,
        ];
    }
}