<?php

namespace Tests\Feature\Backend\Taxonomy;

use App\Domains\Auth\Models\User;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
use App\Domains\Taxonomy\Models\TaxonomyPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

class TaxonomyHistoryPaginationTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_taxonomy_history_displays_paginated_results()
    {
        $this->loginAsAdmin();
        $taxonomy = Taxonomy::factory()->create();

        // Create multiple activity log entries
        for ($i = 0; $i < 20; $i++) {
            activity()
                ->performedOn($taxonomy)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => ['name' => "Update $i"]])
                ->log('updated');
        }

        $response = $this->get(route('dashboard.taxonomy.history', $taxonomy));

        $response->assertStatus(200);
        // Should have pagination links when there are more than 15 items
        $response->assertSee('pagination');
    }

    /** @test */
    public function test_taxonomy_history_pagination_works()
    {
        $this->loginAsAdmin();
        $taxonomy = Taxonomy::factory()->create();

        // Create exactly 20 activity log entries
        for ($i = 0; $i < 20; $i++) {
            activity()
                ->performedOn($taxonomy)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => ['name' => "Update $i"]])
                ->log('updated');
        }

        // First page should have 15 items
        $response = $this->get(route('dashboard.taxonomy.history', $taxonomy));
        $response->assertStatus(200);

        // Second page should exist
        $response = $this->get(route('dashboard.taxonomy.history', $taxonomy) . '?page=2');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_taxonomy_term_history_displays_paginated_results()
    {
        $this->loginAsAdmin();
        $taxonomy = Taxonomy::factory()->create();
        $term = TaxonomyTerm::factory()->create(['taxonomy_id' => $taxonomy->id]);

        // Create multiple activity log entries
        for ($i = 0; $i < 20; $i++) {
            activity()
                ->performedOn($term)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => ['name' => "Update $i"]])
                ->log('updated');
        }

        $response = $this->get(route('dashboard.taxonomy.terms.history', [$taxonomy, $term]));

        $response->assertStatus(200);
        // Should have pagination links when there are more than 15 items
        $response->assertSee('pagination');
    }

    /** @test */
    public function test_taxonomy_term_history_pagination_works()
    {
        $this->loginAsAdmin();
        $taxonomy = Taxonomy::factory()->create();
        $term = TaxonomyTerm::factory()->create(['taxonomy_id' => $taxonomy->id]);

        // Create exactly 20 activity log entries
        for ($i = 0; $i < 20; $i++) {
            activity()
                ->performedOn($term)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => ['name' => "Update $i"]])
                ->log('updated');
        }

        // First page should load
        $response = $this->get(route('dashboard.taxonomy.terms.history', [$taxonomy, $term]));
        $response->assertStatus(200);

        // Second page should exist
        $response = $this->get(route('dashboard.taxonomy.terms.history', [$taxonomy, $term]) . '?page=2');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_taxonomy_page_history_displays_paginated_results()
    {
        $this->loginAsAdmin();
        $taxonomyPage = TaxonomyPage::factory()->create();

        // Create multiple activity log entries
        for ($i = 0; $i < 20; $i++) {
            activity()
                ->performedOn($taxonomyPage)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => ['slug' => "update-$i"]])
                ->log('updated');
        }

        $response = $this->get(route('dashboard.taxonomy-pages.history', $taxonomyPage));

        $response->assertStatus(200);
        // Should have pagination links when there are more than 15 items
        $response->assertSee('pagination');
    }

    /** @test */
    public function test_taxonomy_page_history_pagination_works()
    {
        $this->loginAsAdmin();
        $taxonomyPage = TaxonomyPage::factory()->create();

        // Create exactly 20 activity log entries
        for ($i = 0; $i < 20; $i++) {
            activity()
                ->performedOn($taxonomyPage)
                ->causedBy(auth()->user())
                ->withProperties(['attributes' => ['slug' => "update-$i"]])
                ->log('updated');
        }

        // First page should load
        $response = $this->get(route('dashboard.taxonomy-pages.history', $taxonomyPage));
        $response->assertStatus(200);

        // Second page should exist
        $response = $this->get(route('dashboard.taxonomy-pages.history', $taxonomyPage) . '?page=2');
        $response->assertStatus(200);
    }

    /** @test */
    public function test_taxonomy_history_page_loads_correctly()
    {
        $this->loginAsAdmin();
        $taxonomy = Taxonomy::factory()->create();

        $response = $this->get(route('dashboard.taxonomy.history', $taxonomy));

        $response->assertStatus(200);
        $response->assertSee('Change History for');
    }

    /** @test */
    public function test_taxonomy_term_history_page_loads_correctly()
    {
        $this->loginAsAdmin();
        $taxonomy = Taxonomy::factory()->create();
        $term = TaxonomyTerm::factory()->create(['taxonomy_id' => $taxonomy->id]);

        $response = $this->get(route('dashboard.taxonomy.terms.history', [$taxonomy, $term]));

        $response->assertStatus(200);
        $response->assertSee('Change History for');
    }

    /** @test */
    public function test_taxonomy_page_history_page_loads_correctly()
    {
        $this->loginAsAdmin();
        $taxonomyPage = TaxonomyPage::factory()->create();

        $response = $this->get(route('dashboard.taxonomy-pages.history', $taxonomyPage));

        $response->assertStatus(200);
        $response->assertSee('Change History for');
    }
}
