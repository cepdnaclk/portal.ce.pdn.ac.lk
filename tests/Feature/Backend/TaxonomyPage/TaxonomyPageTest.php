<?php

namespace Tests\Feature\Backend\TaxonomyPage;

use App\Domains\Auth\Models\User;
use App\Domains\Taxonomy\Models\TaxonomyPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TaxonomyPageTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function admin_can_access_taxonomy_page_listing_page(): void
  {
    $this->loginAsAdmin();
    $response = $this->get(route('dashboard.taxonomy-pages.index'));
    $response->assertOk();
  }

  /** @test */
  public function admin_can_access_taxonomy_page_creation_page(): void
  {
    $this->loginAsAdmin();
    $response = $this->get(route('dashboard.taxonomy-pages.create'));
    $response->assertOk();
  }

  /** @test */
  public function admin_can_access_taxonomy_page_edit_page(): void
  {
    $this->loginAsAdmin();
    $page = TaxonomyPage::factory()->create();
    $response = $this->get(route('dashboard.taxonomy-pages.edit', $page));
    $response->assertOk();
  }

  /** @test */
  public function admin_can_access_taxonomy_page_view_page(): void
  {
    $this->loginAsAdmin();
    $page = TaxonomyPage::factory()->create();
    $response = $this->get(route('dashboard.taxonomy-pages.view', $page));
    $response->assertOk();
  }

  /** @test */
  public function admin_can_access_taxonomy_page_delete_confirmation_page(): void
  {
    $this->loginAsAdmin();
    $page = TaxonomyPage::factory()->create();
    $response = $this->get(route('dashboard.taxonomy-pages.delete', $page));
    $response->assertOk();
  }

  /** @test */
  public function guest_cannot_access_taxonomy_page_routes(): void
  {
    $response = $this->get(route('dashboard.taxonomy-pages.create'));
    $response->assertStatus(302)->assertRedirect('/login');

    $response = $this->get(route('dashboard.taxonomy-pages.index'));
    $response->assertStatus(302)->assertRedirect('/login');

    $response = $this->post(route('dashboard.taxonomy-pages.store'), []);
    $response->assertStatus(302)->assertRedirect('/login');
  }

  /** @test */
  public function non_admin_user_cannot_access_taxonomy_page_management_pages_and_actions(): void
  {
    $user = User::factory()->create();
    $this->actingAs($user);

    $page = TaxonomyPage::factory()->create();

    $response = $this->get(route('dashboard.taxonomy-pages.create'));
    $response->assertStatus(302);

    $response = $this->get(route('dashboard.taxonomy-pages.index'));
    $response->assertStatus(302);

    $response = $this->get(route('dashboard.taxonomy-pages.edit', $page));
    $response->assertStatus(302);

    $response = $this->get(route('dashboard.taxonomy-pages.view', $page));
    $response->assertStatus(302);

    $response = $this->get(route('dashboard.taxonomy-pages.delete', $page));
    $response->assertStatus(302);

    $response = $this->post(route('dashboard.taxonomy-pages.store'), ['slug' => 'new-page', 'html' => '<div></div>']);
    $response->assertStatus(302);

    $response = $this->put(route('dashboard.taxonomy-pages.update', $page), ['slug' => 'updated-page']);
    $response->assertStatus(302);

    $response = $this->delete(route('dashboard.taxonomy-pages.destroy', $page));
    $response->assertStatus(302);
  }
}