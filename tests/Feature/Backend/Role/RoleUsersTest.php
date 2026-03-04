<?php

namespace Tests\Feature\Backend\Role;

use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleUsersTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function an_admin_can_access_the_role_users_page()
  {
    $this->loginAsAdmin();
    $role = Role::factory()->create();

    $this->get("/dashboard/auth/role/{$role->id}/users")
      ->assertOk()
      ->assertSee(__('Users Assigned to :role', ['role' => $role->name]));
  }

  /** @test */
  public function only_admin_can_view_role_users_page()
  {
    $role = Role::factory()->create();
    $this->actingAs(User::factory()->admin()->create());

    $response = $this->get("/dashboard/auth/role/{$role->id}/users");

    $response->assertSessionHas('flash_danger', __('You do not have access to do that.'));
  }
}
