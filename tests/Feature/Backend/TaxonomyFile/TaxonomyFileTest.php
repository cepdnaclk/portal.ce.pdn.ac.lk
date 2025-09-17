<?php

namespace Tests\Feature\Backend\TaxonomyFile;

use App\Domains\Auth\Models\User;
use App\Domains\Taxonomy\Models\TaxonomyFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonomyFileTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_access_taxonomy_file_listing_page(): void
    {
        $this->loginAsAdmin();
        $response = $this->get(route('dashboard.taxonomy-files.index'));
        $response->assertOk();
    }

    /** @test */
    public function admin_can_access_taxonomy_file_creation_page(): void
    {
        $this->loginAsAdmin();
        $response = $this->get(route('dashboard.taxonomy-files.create'));
        $response->assertOk();
    }

    /** @test */
    public function admin_can_access_taxonomy_file_edit_page(): void
    {
        $this->loginAsAdmin();
        $file = TaxonomyFile::factory()->create();
        $response = $this->get(route('dashboard.taxonomy-files.edit', $file));
        $response->assertOk();
    }

    /** @test */
    public function admin_can_access_taxonomy_file_view_page(): void
    {
        $this->loginAsAdmin();
        $file = TaxonomyFile::factory()->create();
        $response = $this->get(route('dashboard.taxonomy-files.view', $file));
        $response->assertOk();
    }

    /** @test */
    public function admin_can_access_taxonomy_file_delete_confirmation_page(): void
    {
        $this->loginAsAdmin();
        $file = TaxonomyFile::factory()->create();
        $response = $this->get(route('dashboard.taxonomy-files.delete', $file));
        $response->assertOk();
    }

    /** @test */
    public function guest_cannot_access_taxonomy_file_routes(): void
    {
        $response = $this->get(route('dashboard.taxonomy-files.create'));
        $response->assertStatus(302)->assertRedirect('/login');

        $response = $this->get(route('dashboard.taxonomy-files.index'));
        $response->assertStatus(302)->assertRedirect('/login');

        $response = $this->post(route('dashboard.taxonomy-files.store'), []);
        $response->assertStatus(302)->assertRedirect('/login');
    }

    /** @test */
    public function non_admin_user_cannot_access_taxonomy_file_management_pages_and_actions(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $file = TaxonomyFile::factory()->create();

        $response = $this->get(route('dashboard.taxonomy-files.create'));
        $response->assertStatus(302);

        $response = $this->get(route('dashboard.taxonomy-files.index'));
        $response->assertStatus(302);

        $response = $this->get(route('dashboard.taxonomy-files.edit', $file));
        $response->assertStatus(302);

        $response = $this->get(route('dashboard.taxonomy-files.view', $file));
        $response->assertStatus(302);

        $response = $this->get(route('dashboard.taxonomy-files.delete', $file));
        $response->assertStatus(302);

        $response = $this->post(route('dashboard.taxonomy-files.store'), ['file_name' => 'test.pdf']);
        $response->assertStatus(302);

        $response = $this->put(route('dashboard.taxonomy-files.update', $file), ['file_name' => 'updated.pdf']);
        $response->assertStatus(302);

        $response = $this->delete(route('dashboard.taxonomy-files.destroy', $file));
        $response->assertStatus(302);
    }
}
