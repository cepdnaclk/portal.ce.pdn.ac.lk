<?php

namespace Tests\Unit\Domains\Auth\Listeners;

use App\Domains\Auth\Events\User\UserCreated;
use App\Domains\Auth\Listeners\AssignDepartmentRole;
use App\Domains\Auth\Models\User;
use App\Domains\Auth\Models\Role;
use App\Services\DepartmentDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Mockery;

class AssignDepartmentRoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_assigns_role_returned_by_department_service()
    {
        Role::factory()->create([
            'name' => 'Lecturer',
            'type' => User::TYPE_USER,
        ]);

        $user = User::factory()->user()->create([
            'email' => 'staff@example.com',
        ]);

        $service = Mockery::mock(DepartmentDataService::class);
        $service->shouldReceive('getRoleForEmail')
            ->once()
            ->with('staff@example.com')
            ->andReturn('Lecturer');

        $listener = new AssignDepartmentRole($service);
        $listener->handle(new UserCreated($user));

        $this->assertTrue($user->hasRole('Lecturer'));
    }

    /** @test */
    public function it_does_not_assign_role_when_service_returns_null()
    {
        Role::factory()->create([
            'name' => 'Lecturer',
            'type' => User::TYPE_USER,
        ]);

        $user = User::factory()->user()->create([
            'email' => 'other@example.com',
        ]);

        $service = Mockery::mock(DepartmentDataService::class);
        $service->shouldReceive('getRoleForEmail')
            ->once()
            ->with('other@example.com')
            ->andReturn(null);

        $listener = new AssignDepartmentRole($service);
        $listener->handle(new UserCreated($user));

        $this->assertFalse($user->hasRole('Lecturer'));
    }
}
