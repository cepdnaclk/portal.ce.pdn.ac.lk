<?php

namespace Tests\Feature\API;

use App\Domains\Taxonomy\Models\Taxonomy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonomyApiControllerTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function test_taxonomy_api_returns_only_visible_taxonomies()
  {
    Taxonomy::factory()->count(3)->create(['visibility' => true]);
    Taxonomy::factory()->count(2)->create(['visibility' => false]);

    $response = $this->getJson('/api/taxonomy/v1/');

    $expected = Taxonomy::where('visibility', true)->count();
    $response->assertStatus(200)
      ->assertJsonCount($expected, 'data');
  }

  /** @test */
  public function test_hidden_taxonomy_is_not_accessible()
  {
    $taxonomy = Taxonomy::factory()->create(['visibility' => false]);

    $response = $this->getJson('/api/taxonomy/v1/' . $taxonomy->code);

    $response->assertStatus(404);
  }
}
