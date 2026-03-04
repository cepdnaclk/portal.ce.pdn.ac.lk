<?php

namespace Tests\Feature\Backend\Services;

use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\User;
use App\Domains\Email\Models\ApiKey;
use App\Domains\Email\Models\PortalApp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PortalAppManagementTest extends TestCase
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
  public function users_without_permission_cannot_access_portal_apps()
  {
    $this->actingAs(User::factory()->user()->create());

    $response = $this->get(route('dashboard.services.apps'));

    $response->assertRedirect(route('dashboard.home'));
    $response->assertSessionHas('flash_danger', __('You do not have access to do that.'));
  }

  /** @test */
  public function email_service_manager_can_view_portal_apps()
  {
    $this->loginAsEmailServiceManager();

    $response = $this->get(route('dashboard.services.apps'));

    $response->assertOk();
    $response->assertSee('App Management');
  }

  /** @test */
  public function email_service_manager_can_create_portal_app()
  {
    $this->loginAsEmailServiceManager();

    $response = $this->post(route('dashboard.services.apps.store'), [
      'name' => 'New Portal App',
    ]);

    $response->assertRedirect(route('dashboard.services.apps'));
    $response->assertSessionHas('Success', 'Portal app created.');

    $this->assertDatabaseHas('portal_apps', [
      'name' => 'New Portal App',
      'status' => PortalApp::STATUS_ACTIVE,
    ]);
  }

  /** @test */
  public function email_service_manager_can_generate_api_key()
  {
    $this->loginAsEmailServiceManager();

    $portalApp = PortalApp::create([
      'name' => 'System A',
      'status' => PortalApp::STATUS_ACTIVE,
    ]);

    $response = $this->post(route('dashboard.services.apps.keys.generate', $portalApp), [
      'expires_at' => now()->addDays(10)->toDateString(),
    ]);

    $response->assertRedirect(route('dashboard.services.apps.keys', $portalApp));
    $response->assertSessionHas('new_api_key');

    $this->assertDatabaseHas('api_keys', [
      'portal_app_id' => $portalApp->id,
    ]);
  }

  /** @test */
  public function email_service_manager_can_revoke_api_key()
  {
    $this->loginAsEmailServiceManager();

    $portalApp = PortalApp::create([
      'name' => 'System A',
      'status' => PortalApp::STATUS_ACTIVE,
    ]);

    [$apiKey] = ApiKey::issue($portalApp);

    $response = $this->post(route('dashboard.services.apps.keys.revoke', $apiKey));

    $response->assertRedirect(route('dashboard.services.apps.keys', $portalApp));
    $response->assertSessionHas('Success', 'API key revoked.');

    $this->assertNotNull($apiKey->fresh()->revoked_at);
  }

  /** @test */
  public function email_service_manager_can_delete_portal_app()
  {
    $this->loginAsEmailServiceManager();

    $portalApp = PortalApp::create([
      'name' => 'System A',
      'status' => PortalApp::STATUS_ACTIVE,
    ]);

    $response = $this->delete(route('dashboard.services.apps.destroy', $portalApp));

    $response->assertRedirect(route('dashboard.services.apps'));
    $response->assertSessionHas('Success', 'Portal app deleted.');

    $this->assertDatabaseMissing('portal_apps', ['id' => $portalApp->id]);
  }
}