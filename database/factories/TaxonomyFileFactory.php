<?php

namespace Database\Factories;

use App\Domains\Taxonomy\Models\TaxonomyFile;


use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Http\UploadedFile;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Auth\Models\User;
use App\Domains\Tenant\Models\Tenant;


class TaxonomyFileFactory extends Factory
{
  protected $model = TaxonomyFile::class;

  public function definition(): array
  {
    $fileName = $this->faker->word . '.pdf';
    UploadedFile::fake()->create($fileName);

    return [
      'file_name' => $fileName,
      'file_path' => "taxonomy_files/$fileName",
      'taxonomy_id' => $this->faker->boolean ? Taxonomy::factory() : null,
      'tenant_id' => Tenant::defaultId() ?? Tenant::factory(),
      'metadata' => ['file_size' => $this->faker->numberBetween(100, 5000)],
      'created_by' => User::factory(),
    ];
  }
}
