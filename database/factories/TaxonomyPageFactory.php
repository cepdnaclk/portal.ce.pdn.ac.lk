<?php

namespace Database\Factories;

use App\Domains\Taxonomy\Models\TaxonomyPage;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Auth\Models\User;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxonomyPageFactory extends Factory
{
  protected $model = TaxonomyPage::class;

  public function definition(): array
  {
    $htmlContent = '<div>' . $this->faker->paragraphs(3, true) . '</div>';

    return [
      'slug' => $this->faker->unique()->slug,
      'html' => $htmlContent,
      'taxonomy_id' => $this->faker->boolean ? Taxonomy::factory()->create()->id : null,
      'tenant_id' => Tenant::defaultId() ?? Tenant::factory(),
      'metadata' => [],
      'created_by' => User::factory(),

    ];
  }
}
