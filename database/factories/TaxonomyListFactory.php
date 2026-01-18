<?php

namespace Database\Factories;

use App\Domains\Auth\Models\User;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyList;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxonomyListFactory extends Factory
{
  protected $model = TaxonomyList::class;

  public function definition(): array
  {
    return [
      'name' => $this->faker->unique()->sentence(3),
      'taxonomy_id' => $this->faker->boolean ? Taxonomy::factory() : null,
      'tenant_id' => Tenant::defaultId() ?? Tenant::factory(),
      'data_type' => 'string',
      'items' => [],
      'created_by' => User::factory(),
      'updated_by' => null,
    ];
  }

  public function fileType(): self
  {
    return $this->state(fn() => ['data_type' => 'file']);
  }

  public function pageType(): self
  {
    return $this->state(fn() => ['data_type' => 'page']);
  }
}
