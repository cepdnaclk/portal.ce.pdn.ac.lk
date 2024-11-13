<?php

namespace Database\Factories;

use App\Domains\Auth\Models\User;
use App\Domains\AcademicProgram\Course\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class CourseFactory.
 */
class CourseFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Course::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $academicProgram = $this->faker->randomElement(array_keys(Course::getAcademicPrograms()));
        return [
            'code' => $this->faker->unique()->regexify('[A-Z]{4}[0-9]{4}'),
            'semester_id' => $this->faker->numberBetween(1, 8),
            'academic_program' => $academicProgram,
            'version' => $this->faker->randomElement(
                array_keys(Course::getVersions($academicProgram))
            ),
            'name' => $this->faker->sentence(3),
            'credits' => $this->faker->numberBetween(1, 6),
            'type' => $this->faker->randomElement(array_keys(Course::getTypes())),
            'teaching_methods' => $this->faker->sentence(3),
            'faq_page' => $this->faker->url,
            'content' => $this->faker->paragraph(),
            'objectives' => json_encode([$this->faker->sentence(), $this->faker->sentence()]),
            'time_allocation' => json_encode(['lectures' => $this->faker->numberBetween(10, 50), 'practicals' => $this->faker->numberBetween(5, 20)]),
            'marks_allocation' => json_encode(['assignments' => $this->faker->numberBetween(10, 30), 'exams' => $this->faker->numberBetween(40, 60)]),
            'ilos' => json_encode([$this->faker->sentence(), $this->faker->sentence()]),
            'references' => json_encode([$this->faker->sentence(), $this->faker->sentence()]),
            'created_by' => User::inRandomOrder()->first()->id,
            'updated_by' => User::inRandomOrder()->first()->id,
            'created_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
            'updated_at' => now(),
        ];
    }
}