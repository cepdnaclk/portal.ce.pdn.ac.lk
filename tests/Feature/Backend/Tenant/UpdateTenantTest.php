<?php

namespace Tests\Feature\Backend\Tenant;

use App\Domains\Auth\Models\User;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UpdateTenantTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function a_tenant_can_be_updated()
  {
    $this->loginAsAdmin();

    $tenant = Tenant::factory()->create();

    $this->patch("/dashboard/tenants/{$tenant->id}", [
      'slug' => 'updated-tenant',
      'name' => 'Updated Tenant',
      'url' => 'https://updated-tenant.example.test',
      'description' => 'Updated description',
    ]);

    $this->assertDatabaseHas('tenants', [
      'id' => $tenant->id,
      'slug' => 'updated-tenant',
      'name' => 'Updated Tenant',
      'url' => 'https://updated-tenant.example.test',
      'description' => 'Updated description',
    ]);
  }

  /** @test */
  public function the_slug_must_be_unique_on_update()
  {
    $this->loginAsAdmin();

    $tenant = Tenant::factory()->create();
    $otherTenant = Tenant::factory()->create();

    $response = $this->patch("/dashboard/tenants/{$tenant->id}", [
      'slug' => $otherTenant->slug,
      'name' => 'Updated Tenant',
      'url' => 'https://updated-tenant.example.test',
      'description' => 'Updated description',
    ]);

    $response->assertSessionHasErrors('slug');
  }

  /** @test */
  public function only_admin_can_update_tenants()
  {
    $nonAdminUser = User::factory()->createOne();
    $this->actingAs($nonAdminUser);

    $tenant = Tenant::factory()->create();

    $response = $this->patch("/dashboard/tenants/{$tenant->id}", [
      'slug' => 'updated-tenant',
      'name' => 'Updated Tenant',
      'url' => 'https://updated-tenant.example.test',
      'description' => 'Updated description',
    ]);

    $response->assertSessionHas('flash_danger', __('You do not have access to do that.'));
  }
}