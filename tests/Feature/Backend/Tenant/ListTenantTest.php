<?php

namespace Tests\Feature\Backend\Tenant;

use App\Domains\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ListTenantTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function an_admin_can_access_the_tenant_index_page()
  {
    $this->loginAsAdmin();

    $this->get('/dashboard/tenants')->assertOk();
  }

  /** @test */
  public function only_admin_can_view_tenants()
  {
    $this->actingAs(User::factory()->admin()->create());

    $response = $this->get('/dashboard/tenants');

    $response->assertSessionHas('flash_danger', __('You do not have access to do that.'));
  }
}
