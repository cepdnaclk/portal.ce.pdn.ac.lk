<?php

namespace Tests\Feature\Backend\TaxonomyTerm;

use App\Domains\Auth\Models\User;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonomyTermTest extends TestCase
{
    use RefreshDatabase;

    private function createTaxonomy(): Taxonomy
    {
        return Taxonomy::factory()->create([
            'properties' => [
                ['code' => 'title', 'name' => 'Title', 'data_type' => 'string'],
            ],
        ]);
    }

    /** @test */
    public function test_admin_can_access_taxonomy_term_listing_page()
    {
        $this->loginAsAdmin();
        $taxonomy = $this->createTaxonomy();
        $response = $this->get(route('dashboard.taxonomy.terms.index', $taxonomy));
        $response->assertOk();
    }

    /** @test */
    public function test_admin_can_access_taxonomy_term_creation_page()
    {
        $this->loginAsAdmin();
        $taxonomy = $this->createTaxonomy();
        $response = $this->get(route('dashboard.taxonomy.terms.create', $taxonomy));
        $response->assertOk();
    }

    /** @test */
    public function test_admin_can_access_taxonomy_term_edit_page()
    {
        $this->loginAsAdmin();
        $taxonomy = $this->createTaxonomy();
        $term = TaxonomyTerm::factory()->create(['taxonomy_id' => $taxonomy->id, 'metadata' => []]);
        $response = $this->get(route('dashboard.taxonomy.terms.edit', ['taxonomy' => $taxonomy, 'term' => $term]));
        $response->assertOk();
    }

    /** @test */
    public function test_admin_can_access_taxonomy_term_delete_confirmation_page()
    {
        $this->loginAsAdmin();
        $taxonomy = $this->createTaxonomy();
        $term = TaxonomyTerm::factory()->create(['taxonomy_id' => $taxonomy->id, 'metadata' => []]);
        $response = $this->get(route('dashboard.taxonomy.terms.delete', ['taxonomy' => $taxonomy, 'term' => $term]));
        $response->assertOk();
    }

    /** @test */
    public function test_admin_can_access_taxonomy_term_history_page()
    {
        $this->loginAsAdmin();
        $taxonomy = $this->createTaxonomy();
        $term = TaxonomyTerm::factory()->create(['taxonomy_id' => $taxonomy->id, 'metadata' => []]);
        $response = $this->get(route('dashboard.taxonomy.terms.history', ['taxonomy' => $taxonomy, 'term' => $term]));
        $response->assertOk();
    }

    /** @test */
    public function test_guest_cannot_access_taxonomy_term_routes()
    {
        $taxonomy = $this->createTaxonomy();

        $response = $this->get(route('dashboard.taxonomy.terms.create', $taxonomy));
        $response->assertStatus(302)->assertRedirect('/login');

        $response = $this->get(route('dashboard.taxonomy.terms.index', $taxonomy));
        $response->assertStatus(302)->assertRedirect('/login');

        $response = $this->post(route('dashboard.taxonomy.terms.store', $taxonomy), []);
        $response->assertStatus(302)->assertRedirect('/login');
    }

    /** @test */
    public function test_non_admin_user_cannot_access_taxonomy_term_management_pages_and_actions()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $taxonomy = $this->createTaxonomy();
        $term = TaxonomyTerm::factory()->create(['taxonomy_id' => $taxonomy->id, 'metadata' => []]);

        $response = $this->get(route('dashboard.taxonomy.terms.create', $taxonomy));
        $response->assertStatus(302);

        $response = $this->get(route('dashboard.taxonomy.terms.index', $taxonomy));
        $response->assertStatus(302);

        $response = $this->get(route('dashboard.taxonomy.terms.edit', ['taxonomy' => $taxonomy, 'term' => $term]));
        $response->assertStatus(302);

        $response = $this->get(route('dashboard.taxonomy.terms.delete', ['taxonomy' => $taxonomy, 'term' => $term]));
        $response->assertStatus(302);

        $response = $this->get(route('dashboard.taxonomy.terms.history', ['taxonomy' => $taxonomy, 'term' => $term]));
        $response->assertStatus(302);

        $response = $this->post(route('dashboard.taxonomy.terms.store', $taxonomy), ['code' => 'test', 'name' => 'Test']);
        $response->assertStatus(302);

        $response = $this->put(route('dashboard.taxonomy.terms.update', ['taxonomy' => $taxonomy, 'term' => $term]), ['code' => 'updated', 'name' => 'Updated']);
        $response->assertStatus(302);

        $response = $this->delete(route('dashboard.taxonomy.terms.destroy', ['taxonomy' => $taxonomy, 'term' => $term]));
        $response->assertStatus(302);
    }
}

