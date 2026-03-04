<?php

namespace Tests\Feature\Backend\Tenant;

use App\Domains\Auth\Models\User;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TenantUsersTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function an_admin_can_access_the_tenant_users_page()
  {
    $this->loginAsAdmin();
    $tenant = Tenant::factory()->create();

    $this->get("/dashboard/tenants/{$tenant->id}/users")
      ->assertOk()
      ->assertSee(__('Users Assigned to :tenant', ['tenant' => $tenant->name]));
  }

  /** @test */
  public function only_admin_can_view_tenant_users_page()
  {
    $tenant = Tenant::factory()->create();
    $this->actingAs(User::factory()->create());

    $response = $this->get("/dashboard/tenants/{$tenant->id}/users");

    $response->assertSessionHas('flash_danger', __('You do not have access to do that.'));
  }
}
