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

class AssignTemporaryStaffRoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_assigns_temporary_academic_staff_role()
    {
        Cache::flush();

        Role::factory()->create([
            'name' => 'Temporary Academic Staff',
            'type' => User::TYPE_USER,
        ]);

        $user = User::factory()->user()->create([
            'email' => 'temp.staff@example.com',
        ]);

        Http::fake([
            config('constants.department_data.base_url') . '/people/v1/staff/all/' => Http::response([
                [
                    'email' => 'temp.staff@example.com',
                    'designation' => 'Temporary Academic Staff',
                ],
            ]),
            config('constants.department_data.base_url') . '/people/v1/students/all/' => Http::response([]),
        ]);

        $listener = new UserEventListener();
        $listener->onCreated(new UserCreated($user));

        $this->assertTrue($user->hasRole('Temporary Academic Staff'));
    }

    /** @test */
    public function it_assigns_temporary_academic_staff_role_for_temporary_lecturer()
    {
        Cache::flush();

        Role::factory()->create([
            'name' => 'Temporary Academic Staff',
            'type' => User::TYPE_USER,
        ]);

        $user = User::factory()->user()->create([
            'email' => 'temp.lecturer@example.com',
        ]);

        Http::fake([
            config('constants.department_data.base_url') . '/people/v1/staff/all/' => Http::response([
                [
                    'email' => 'temp.lecturer@example.com',
                    'designation' => 'Temporary Lecturer',
                ],
            ]),
            config('constants.department_data.base_url') . '/people/v1/students/all/' => Http::response([]),
        ]);

        $listener = new UserEventListener();
        $listener->onCreated(new UserCreated($user));

        $this->assertTrue($user->hasRole('Temporary Academic Staff'));
    }

    /** @test */
    public function it_assigns_temporary_academic_staff_role_for_visiting_lecturer()
    {
        Cache::flush();

        Role::factory()->create([
            'name' => 'Temporary Academic Staff',
            'type' => User::TYPE_USER,
        ]);

        $user = User::factory()->user()->create([
            'email' => 'visiting.lecturer@example.com',
        ]);

        Http::fake([
            config('constants.department_data.base_url') . '/people/v1/staff/all/' => Http::response([
                [
                    'email' => 'visiting.lecturer@example.com',
                    'designation' => 'Visiting Lecturer',
                ],
            ]),
            config('constants.department_data.base_url') . '/people/v1/students/all/' => Http::response([]),
        ]);

        $listener = new UserEventListener();
        $listener->onCreated(new UserCreated($user));

        $this->assertTrue($user->hasRole('Temporary Academic Staff'));
    }
}
