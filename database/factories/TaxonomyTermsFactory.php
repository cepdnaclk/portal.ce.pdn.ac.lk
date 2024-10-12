<?php

namespace Database\Factories;
use App\Domains\TaxonomyTerms\Models\TaxonomyTerms;
use App\Domains\Auth\Models\User;



use Illuminate\Database\Eloquent\Factories\Factory;

class TaxonomyTermsFactory extends Factory
{



    protected $model = TaxonomyTerms::class;

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
            'taxonomy_id' => \App\Domains\Taxonomy\Models\Taxonomy::factory(), 
            'parent_id' => null, 
            'metadata' => json_encode([
                [
                    'code' => 'country_name',
                    'value' => $this->faker->country                
                ],
                [
                    'code' => 'country_code',
                    'value' => $this->faker->optional()->randomNumber(2, true)
                ],
                [
                    'code' => 'visible',
                    'value' => $this->faker->boolean
                ]
            ]), 
            'created_by' => User::inRandomOrder()->first()->id,
            'updated_by' => User::inRandomOrder()->first()->id,
            'created_at' => now(), 
            'updated_at' => now(),  
        ];
    }
}
