<?php

namespace Tests\Feature\Backend\TaxonomyPage;

use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyPage;
use App\Domains\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonomyPageTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_admin_can_access_taxonomy_page_listing_page()
    {
        $this->loginAsAdmin();
        $response = $this->get(route('dashboard.taxonomy-pages.index'));
        $response->assertOk();
    }

    /** @test */
    public function test_admin_can_access_taxonomy_page_creation_page()
    {
        $this->loginAsAdmin();
        $response = $this->get(route('dashboard.taxonomy-pages.create'));
        $response->assertOk();
    }

    /** @test */
    public function test_admin_can_access_taxonomy_page_edit_page()
    {
        $this->loginAsAdmin();
        $taxonomyPage = TaxonomyPage::factory()->create();
        $response = $this->get(route('dashboard.taxonomy-pages.edit', $taxonomyPage));
        $response->assertOk();
    }

    /** @test */
    public function test_admin_can_access_taxonomy_page_view_page()
    {
        $this->loginAsAdmin();
        $taxonomyPage = TaxonomyPage::factory()->create();
        $response = $this->get(route('dashboard.taxonomy-pages.view', $taxonomyPage));
        $response->assertOk();
    }

    /** @test */
    public function test_admin_can_access_taxonomy_page_delete_confirmation_page()
    {
        $this->loginAsAdmin();
        $taxonomyPage = TaxonomyPage::factory()->create();
        $response = $this->get(route('dashboard.taxonomy-pages.delete', $taxonomyPage));
        $response->assertOk();
    }

    /** @test */
    public function test_admin_can_create_taxonomy_page()
    {
        $this->loginAsAdmin();
        $taxonomy = Taxonomy::factory()->create();

        $data = [
            'slug' => 'test-page',
            'html' => '<div>Test Content</div>',
            'taxonomy_id' => $taxonomy->id,
        ];

        $response = $this->post(route('dashboard.taxonomy-pages.store'), $data);

        $response->assertStatus(302);
        $this->assertDatabaseHas('taxonomy_pages', ['slug' => 'test-page']);
    }

    /** @test */
    public function test_admin_can_view_taxonomy_page()
    {
        $this->loginAsAdmin();
        $taxonomyPage = TaxonomyPage::factory()->create();
        $response = $this->get(route('dashboard.taxonomy-pages.view', $taxonomyPage));

        $response->assertStatus(200);
        $response->assertSee($taxonomyPage->slug);
    }

    /** @test */
    public function test_admin_can_delete_taxonomy_page()
    {
        $this->loginAsAdmin();
        $taxonomyPage = TaxonomyPage::factory()->create();

        $this->assertDatabaseHas('taxonomy_pages', ['id' => $taxonomyPage->id]);

        $response = $this->delete(route('dashboard.taxonomy-pages.destroy', $taxonomyPage));

        $response->assertStatus(302);
        $this->assertDatabaseMissing('taxonomy_pages', ['id' => $taxonomyPage->id]);
    }

    /** @test */
    public function test_guest_cannot_access_taxonomy_page_routes()
    {
        $response = $this->get(route('dashboard.taxonomy-pages.create'));
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        $response = $this->get(route('dashboard.taxonomy-pages.index'));
        $response->assertStatus(302);
        $response->assertRedirect('/login');

        $response = $this->post(route('dashboard.taxonomy-pages.store'), []);
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function test_non_admin_user_cannot_access_taxonomy_page_management_pages_and_actions()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('dashboard.taxonomy-pages.create'));
        $response->assertStatus(302);

        $response = $this->get(route('dashboard.taxonomy-pages.index'));
        $response->assertStatus(302);

        $taxonomyPage = TaxonomyPage::factory()->create();
        $response = $this->get(route('dashboard.taxonomy-pages.edit', $taxonomyPage));
        $response->assertStatus(302);

        $response = $this->get(route('dashboard.taxonomy-pages.view', $taxonomyPage));
        $response->assertStatus(302);

        $response = $this->get(route('dashboard.taxonomy-pages.delete', $taxonomyPage));
        $response->assertStatus(302);

        $response = $this->post(route('dashboard.taxonomy-pages.store'), ['slug' => 'new-page', 'html' => '<div></div>']);
        $response->assertStatus(302);

        $response = $this->put(route('dashboard.taxonomy-pages.update', $taxonomyPage), ['slug' => 'updated-page']);
        $response->assertStatus(302);

        $response = $this->delete(route('dashboard.taxonomy-pages.destroy', $taxonomyPage));
        $response->assertStatus(302);
    }
}
