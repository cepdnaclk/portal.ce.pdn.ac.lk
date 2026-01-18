<?php

namespace Tests\Feature\API\V2;

use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
use App\Domains\Tenant\Models\Tenant;
use Tests\TestCase;

class TaxonomyApiControllerTest extends TestCase
{
  /** @test */
  public function test_v2_taxonomy_index_filters_by_tenant()
  {
    $tenant = Tenant::factory()->create(['slug' => 'tenant-a']);
    $otherTenant = Tenant::factory()->create(['slug' => 'tenant-b']);

    $visibleTenantTaxonomy = Taxonomy::factory()->create([
      'visibility' => true,
      'tenant_id' => $tenant->id,
      'code' => 'tenant_a_tax',
    ]);
    Taxonomy::factory()->create([
      'visibility' => true,
      'tenant_id' => $otherTenant->id,
      'code' => 'tenant_b_tax',
    ]);

    $response = $this->getJson("/api/taxonomy/v2/{$tenant->slug}");

    $response->assertStatus(200)
      ->assertJsonFragment(['code' => $visibleTenantTaxonomy->code])
      ->assertJsonMissing(['code' => 'tenant_b_tax']);
  }

  /** @test */
  public function test_v2_taxonomy_get_returns_only_visible_taxonomy_for_tenant()
  {
    $tenant = Tenant::factory()->create(['slug' => 'tenant-a']);
    $taxonomy = Taxonomy::factory()->create([
      'visibility' => true,
      'tenant_id' => $tenant->id,
      'code' => 'tenant_a_visible',
    ]);

    $response = $this->getJson("/api/taxonomy/v2/{$tenant->slug}/{$taxonomy->code}");

    $response->assertStatus(200)
      ->assertJsonFragment(['code' => $taxonomy->code]);
  }

  /** @test */
  public function test_v2_taxonomy_get_rejects_hidden_taxonomy_for_tenant()
  {
    $tenant = Tenant::factory()->create(['slug' => 'tenant-a']);
    $taxonomy = Taxonomy::factory()->create([
      'visibility' => false,
      'tenant_id' => $tenant->id,
      'code' => 'tenant_a_hidden',
    ]);

    $response = $this->getJson("/api/taxonomy/v2/{$tenant->slug}/{$taxonomy->code}");

    $response->assertStatus(404);
  }

  /** @test */
  public function test_v2_taxonomy_term_filters_by_tenant()
  {
    $tenant = Tenant::factory()->create(['slug' => 'tenant-a']);
    $otherTenant = Tenant::factory()->create(['slug' => 'tenant-b']);

    $taxonomy = Taxonomy::factory()->create([
      'visibility' => true,
      'tenant_id' => $tenant->id,
      'code' => 'tenant_a_tax_term',
    ]);
    $term = TaxonomyTerm::factory()->create([
      'taxonomy_id' => $taxonomy->id,
      'code' => 'tenant_a_term',
      'metadata' => [],
    ]);

    TaxonomyTerm::factory()->create([
      'taxonomy_id' => Taxonomy::factory()->create([
        'visibility' => true,
        'tenant_id' => $otherTenant->id,
        'code' => 'tenant_b_tax_term',
      ])->id,
      'code' => 'tenant_b_term',
      'metadata' => [],
    ]);

    $response = $this->getJson("/api/taxonomy/v2/{$tenant->slug}/term/{$term->code}");

    $response->assertStatus(200)
      ->assertJsonFragment(['code' => $term->code])
      ->assertJsonFragment(['taxonomy' => $taxonomy->code])
      ->assertJsonMissing(['code' => 'tenant_b_term']);
  }
}
