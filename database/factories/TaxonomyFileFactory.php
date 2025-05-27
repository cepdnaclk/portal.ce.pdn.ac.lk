<?php

namespace Database\Factories;

use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyFile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;

class TaxonomyFileFactory extends Factory
{
    protected $model = TaxonomyFile::class;

    public function definition(): array
    {
        return [
            'file_name' => $this->faker->word . '.pdf',
            'file_path' => UploadedFile::fake()->create('document.pdf')->getPath(),
            'taxonomy_id' => $this->faker->boolean ? Taxonomy::factory() : null,
            'metadata' => ['file_size' => $this->faker->numberBetween(100, 5000)],
            'created_by' => User::factory(),
        ];
    }
}