<?php

namespace Tests\Feature\API;

use App\Domains\Announcement\Models\Announcement;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnouncementApiControllerTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function it_returns_404_for_unknown_tenant()
  {
    $response = $this->getJson('/api/announcements/v2/unknown-tenant');

    $response->assertStatus(404)
      ->assertJson(['message' => 'Tenant not found']);
  }

  /** @test */
  public function it_filters_by_area()
  {
    $tenant = Tenant::factory()->create();

    $frontend = Announcement::factory()
      ->enabled()
      ->frontend()
      ->noDates()
      ->create(['tenant_id' => $tenant->id, 'message' => 'frontend-message']);

    $backend = Announcement::factory()
      ->enabled()
      ->backend()
      ->noDates()
      ->create(['tenant_id' => $tenant->id, 'message' => 'backend-message']);

    $both = Announcement::factory()
      ->enabled()
      ->both()
      ->noDates()
      ->create(['tenant_id' => $tenant->id, 'message' => 'both-message']);

    // Check for frontend area
    $response_frontend = $this->getJson('/api/announcements/v2/' . $tenant->slug . '?area=frontend');
    $response_frontend->assertOk()
      ->assertJsonCount(2, 'data')
      ->assertJsonFragment(['message' => $frontend->message])
      ->assertJsonFragment(['message' => $both->message]);

    // Check for backend area
    $response_backend = $this->getJson('/api/announcements/v2/' . $tenant->slug . '?area=backend');
    $response_backend->assertOk()
      ->assertJsonCount(2, 'data')
      ->assertJsonFragment(['message' => $backend->message])
      ->assertJsonFragment(['message' => $both->message]);

    // Check for both area
    $response_both = $this->getJson('/api/announcements/v2/' . $tenant->slug . '?area=both');
    $response_both->assertOk()
      ->assertJsonCount(1, 'data')
      ->assertJsonFragment(['message' => $both->message]);

    // Check for invalid area type
    $response_invalid = $this->getJson('/api/announcements/v2/' . $tenant->slug . '?area=invalid');
    $response_invalid->assertOk()
      ->assertJsonCount(3, 'data')
      ->assertJsonFragment(['message' => $frontend->message])
      ->assertJsonFragment(['message' => $backend->message])
      ->assertJsonFragment(['message' => $both->message]);
  }

  /** @test */
  public function it_filters_announcements_by_tenant()
  {
    $tenantA = Tenant::factory()->create();
    $tenantB = Tenant::factory()->create();

    $tenantAAnnouncement = Announcement::factory()
      ->enabled()
      ->frontend()
      ->noDates()
      ->create(['tenant_id' => $tenantA->id, 'message' => 'tenant-a-message']);

    Announcement::factory()
      ->enabled()
      ->frontend()
      ->noDates()
      ->create(['tenant_id' => $tenantB->id, 'message' => 'tenant-b-message']);

    $response = $this->getJson('/api/announcements/v2/' . $tenantA->slug);

    $response->assertOk()
      ->assertJsonCount(1, 'data')
      ->assertJsonFragment(['message' => $tenantAAnnouncement->message])
      ->assertJsonMissing(['message' => 'tenant-b-message']);
  }

  /** @test */
  public function it_includes_global_announcements_for_backend_area()
  {
    $tenant = Tenant::factory()->create();

    $backend = Announcement::factory()
      ->enabled()
      ->backend()
      ->noDates()
      ->create(['tenant_id' => $tenant->id, 'message' => 'backend-message']);

    $global = Announcement::factory()
      ->enabled()
      ->global()
      ->noDates()
      ->create(['tenant_id' => $tenant->id, 'message' => 'global-message']);

    $response = $this->getJson('/api/announcements/v2/' . $tenant->slug . '?area=backend');

    $response->assertOk()
      ->assertJsonCount(2, 'data')
      ->assertJsonFragment(['message' => $backend->message])
      ->assertJsonFragment(['message' => $global->message]);
  }
}
