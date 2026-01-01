<?php

namespace Tests\Unit\Domains\Auth\Listeners;

use App\Domains\Auth\Events\User\UserCreated;
use App\Domains\Auth\Listeners\UserEventListener;
use App\Domains\Auth\Models\User;
use App\Domains\Auth\Models\Role;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AssignDepartmentRoleTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function it_assigns_roles_based_on_department_email()
  {
    Cache::flush();

    Role::factory()->create([
      'name' => 'Lecturer',
      'type' => User::TYPE_USER,
    ]);

    $user = User::factory()->user()->create([
      'email' => 'staff@example.com',
    ]);

    Http::fake([
      config('constants.department_data.base_url') . '/people/v1/staff/all/' => Http::response([
        [
          'email' => 'staff@example.com',
          'designation' => 'Lecturer',
        ],
      ]),
    ]);

    $listener = new UserEventListener();
    $listener->onCreated(new UserCreated($user));

    $this->assertTrue($user->hasRole('Lecturer'));
  }

  /** @test */
  public function it_does_not_assign_role_for_non_staff_email()
  {
    Cache::flush();

    Role::factory()->create([
      'name' => 'Lecturer',
      'type' => User::TYPE_USER,
    ]);

    $user = User::factory()->user()->create([
      'email' => 'other@example.com',
    ]);

    Http::fake([
      config('constants.department_data.base_url') . '/people/v1/staff/all/' => Http::response([
        [
          'email' => 'staff1@eng.pdn.ac.lk',
          'designation' => 'Lecturer',
        ],
      ]),
    ]);

    $listener = new UserEventListener();
    $listener->onCreated(new UserCreated($user));

    $this->assertFalse($user->hasRole('Lecturer'));
  }
}