<?php

namespace Tests\Feature\Backend\Services;

use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EmailServiceDashboardTest extends TestCase
{
  use RefreshDatabase;

  protected function loginAsEmailServiceManager(): User
  {
    $role = Role::where('name', 'Email Service Manager')->firstOrFail();
    $user = User::factory()->admin()->create(['name' => 'Email Service Manager']);
    $user->assignRole($role->name);
    $this->actingAs($user);

    return $user;
  }

  /** @test */
  public function users_without_permission_cannot_access_email_history()
  {
    $this->actingAs(User::factory()->user()->create());

    $response = $this->get(route('dashboard.services.email.history'));

    $response->assertRedirect(route('dashboard.home'));
    $response->assertSessionHas('flash_danger', __('You do not have access to do that.'));
  }

  /** @test */
  public function email_service_manager_can_view_email_history()
  {
    $this->loginAsEmailServiceManager();

    $response = $this->get(route('dashboard.services.email.history'));

    $response->assertOk();
    $response->assertSee('Email History');
  }
}