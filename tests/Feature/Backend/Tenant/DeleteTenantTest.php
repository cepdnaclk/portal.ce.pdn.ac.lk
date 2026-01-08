<?php

namespace Tests\Feature\Backend\Tenant;

use App\Domains\Auth\Models\User;
use App\Domains\ContentManagement\Models\News;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeleteTenantTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function a_tenant_can_be_deleted()
  {
    $tenant = Tenant::factory()->create();

    $this->loginAsAdmin();

    $this->assertDatabaseHas('tenants', ['id' => $tenant->id]);

    $this->delete("/dashboard/tenants/{$tenant->id}");

    $this->assertDatabaseMissing('tenants', ['id' => $tenant->id]);
  }

  /** @test */
  public function a_tenant_with_assigned_resources_cant_be_deleted()
  {
    $this->loginAsAdmin();

    $tenant = Tenant::factory()->create();
    News::factory()->create(['tenant_id' => $tenant->id]);

    $response = $this->delete("/dashboard/tenants/{$tenant->id}");

    $response->assertSessionHas(['flash_danger' => __('You can not delete a tenant with associated resources.')]);

    $this->assertDatabaseHas('tenants', ['id' => $tenant->id]);
  }

  /** @test */
  public function only_admin_can_delete_tenants()
  {
    $this->actingAs(User::factory()->admin()->create());

    $tenant = Tenant::factory()->create();

    $response = $this->delete("/dashboard/tenants/{$tenant->id}");

    $response->assertSessionHas('flash_danger', __('You do not have access to do that.'));

    $this->assertDatabaseHas('tenants', ['id' => $tenant->id]);
  }
}
