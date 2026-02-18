<?php

namespace Tests\Feature\Backend\Tenant;

use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\User;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTenantTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function an_admin_can_access_the_create_tenant_page()
  {
    $this->loginAsAdmin();

    $this->get('/dashboard/tenants/create')->assertOk();
  }

  /** @test */
  public function create_tenant_requires_validation()
  {
    $this->loginAsAdmin();

    $response = $this->post('/dashboard/tenants');

    $response->assertSessionHasErrors('slug');
  }

  /** @test */
  public function the_slug_must_be_unique()
  {
    $this->loginAsAdmin();

    $tenant = Tenant::factory()->create();

    $response = $this->post('/dashboard/tenants', [
      'slug' => $tenant->slug,
      'name' => 'Duplicate Tenant',
      'url' => 'https://duplicate.example.test',
    ]);

    $response->assertSessionHasErrors('slug');
  }

  /** @test */
  public function a_tenant_can_be_created()
  {
    $this->loginAsAdmin();

    $this->post('/dashboard/tenants', [
      'slug' => 'new-tenant',
      'name' => 'New Tenant',
      'url' => 'https://new-tenant.example.test',
      'description' => 'Tenant description',
      'is_default' => 1,
    ]);

    $this->assertDatabaseHas('tenants', [
      'slug' => 'new-tenant',
      'name' => 'New Tenant',
      'url' => 'https://new-tenant.example.test',
      'description' => 'Tenant description',
      'is_default' => true,
    ]);
  }

  /** @test */
  public function creating_a_tenant_creates_a_manager_role_by_default()
  {
    $this->loginAsAdmin();

    $this->post('/dashboard/tenants', [
      'slug' => 'ece-tenant',
      'name' => 'ECE',
      'url' => 'https://ece.example.test',
    ]);

    $tenant = Tenant::query()->where('slug', 'ece-tenant')->firstOrFail();

    $this->assertDatabaseHas('roles', [
      'name' => 'ECE Manager',
      'type' => User::TYPE_USER,
      'guard_name' => config('auth.defaults.guard', 'web'),
    ]);

    $role = Role::query()->where('name', 'ECE Manager')->firstOrFail();

    $this->assertDatabaseHas('tenant_role', [
      'tenant_id' => $tenant->id,
      'role_id' => $role->id,
    ]);
  }

  /** @test */
  public function creating_a_tenant_can_skip_manager_role_creation()
  {
    $this->loginAsAdmin();

    $this->post('/dashboard/tenants', [
      'slug' => 'civil-tenant',
      'name' => 'Civil',
      'url' => 'https://civil.example.test',
      'create_manager_role' => 0,
    ]);

    $tenant = Tenant::query()->where('slug', 'civil-tenant')->firstOrFail();

    $this->assertDatabaseMissing('roles', [
      'name' => 'Civil Manager',
    ]);

    $this->assertDatabaseMissing('tenant_role', [
      'tenant_id' => $tenant->id,
    ]);
  }

  /** @test */
  public function only_admin_can_create_tenants()
  {
    $nonAdminUser = User::factory()->createOne();
    $this->actingAs($nonAdminUser);

    $response = $this->get('/dashboard/tenants/create');

    $response->assertSessionHas('flash_danger', __('You do not have access to do that.'));
  }
}
