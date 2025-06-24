<?php

namespace Database\Factories;

use App\Domains\Taxonomy\Models\TaxonomyPage;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxonomyPageFactory extends Factory
{
    protected $model = TaxonomyPage::class;

    public function definition(): array
    {
        return [
            'slug' => $this->faker->unique()->slug,
            'html' => '<p>'.$this->faker->sentence().'</p>',
            'taxonomy_id' => $this->faker->boolean ? Taxonomy::factory() : null,
            'created_by' => User::factory(),
        ];
    }
}
