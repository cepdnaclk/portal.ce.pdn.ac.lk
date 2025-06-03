<?php

namespace Database\Factories;

use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Auth\Models\User; // Added for created_by/updated_by if User::factory() was intended, but original used 1
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Class TaxonomyFactory.
 */
class TaxonomyFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Taxonomy::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'code' => $this->faker->unique()->lexify('????'),
            'name' => $this->faker->word,
            'description' => $this->faker->sentence,
            'properties' => json_encode([
                [
                    'code' => 'country_name',
                    'name' => 'Country',
                    'data_type' => 'string'
                ],
                [
                    'code' => 'country_code',
                    'name' => 'Country Code',
                    'data_type' => 'number'
                ],
                [
                    'code' => 'visible',
                    'name' => 'Visibility',
                    'data_type' => 'boolean'
                ]
            ]),
            'created_by' => 1, // Reverted to original
            'updated_by' => 1, // Reverted to original
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
