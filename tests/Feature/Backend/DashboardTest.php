<?php

namespace Tests\Feature\Backend;

use App\Domains\Auth\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Class DashboardTest.
 */
class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function unauthenticated_users_cant_access_admin_dashboard()
    {
        $this->get('/dashboard/home')->assertRedirect('/login');
    }

    /** @test */
    public function all_users_can_access_admin_dashboard()
    {
        $this->actingAs(User::factory()->user()->create());

        $response = $this->get('/dashboard/home');

        // $response->assertRedirect('/');
        $response->assertStatus(200); // As all auth users can access the dashboard

        // $response->assertSessionHas('flash_danger', __('You do not have access to do that.'));
    }

    /** @test */
    public function admin_can_access_admin_dashboard()
    {
        $this->loginAsAdmin();

        $this->get('/dashboard/home')->assertOk();
    }
}
