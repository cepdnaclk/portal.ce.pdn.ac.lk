<?php

namespace Tests\Feature\API;

use App\Domains\Tenant\Models\Tenant;
use App\Domains\Taxonomy\Models\Taxonomy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonomyApiControllerTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function test_taxonomy_api_returns_only_visible_taxonomies()
  {
    $defaultTenant = Tenant::default();
    $otherTenant = Tenant::factory()->create(['slug' => 'other-tenant']);

    $visibleDefault = Taxonomy::factory()->create([
      'visibility' => true,
      'tenant_id' => $defaultTenant->id,
      'code' => 'visible_default_tax',
    ]);
    Taxonomy::factory()->create([
      'visibility' => false,
      'tenant_id' => $defaultTenant->id,
      'code' => 'hidden_default_tax',
    ]);
    Taxonomy::factory()->create([
      'visibility' => true,
      'tenant_id' => $otherTenant->id,
      'code' => 'visible_other_tax',
    ]);

    $response = $this->getJson('/api/taxonomy/v1/');

    $response->assertStatus(200)
      ->assertJsonFragment(['code' => $visibleDefault->code])
      ->assertJsonMissing(['code' => 'visible_other_tax'])
      ->assertJsonMissing(['code' => 'hidden_default_tax']);
  }

  /** @test */
  public function test_hidden_taxonomy_is_not_accessible()
  {
    $defaultTenant = Tenant::default();
    $taxonomy = Taxonomy::factory()->create([
      'visibility' => false,
      'tenant_id' => $defaultTenant->id,
    ]);

    $response = $this->getJson('/api/taxonomy/v1/' . $taxonomy->code);

    $response->assertStatus(404);
  }

  /** @test */
  public function test_taxonomy_api_does_not_return_non_default_tenant_taxonomy_by_code()
  {
    $otherTenant = Tenant::factory()->create(['slug' => 'other-tenant']);
    $taxonomy = Taxonomy::factory()->create([
      'visibility' => true,
      'tenant_id' => $otherTenant->id,
      'code' => 'other_tenant_code',
    ]);

    $response = $this->getJson('/api/taxonomy/v1/' . $taxonomy->code);

    $response->assertStatus(404);
  }
}