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

class AssignAcademicSupportStaffRoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_assigns_academic_support_staff_role()
    {
        Cache::flush();

        Role::factory()->create([
            'name' => 'Academic Support Staff',
            'type' => User::TYPE_USER,
        ]);

        $user = User::factory()->user()->create([
            'email' => 'support.staff@example.com',
        ]);

        Http::fake([
            config('constants.department_data.base_url') . '/people/v1/staff/all/' => Http::response([
                [
                    'email' => 'support.staff@example.com',
                    'designation' => 'Academic Support Staff',
                ],
            ]),
            config('constants.department_data.base_url') . '/people/v1/students/all/' => Http::response([]),
        ]);

        $listener = new UserEventListener();
        $listener->onCreated(new UserCreated($user));

        $this->assertTrue($user->hasRole('Academic Support Staff'));
    }

    /** @test */
    public function it_assigns_academic_support_staff_role_for_technical_officer()
    {
        Cache::flush();

        Role::factory()->create([
            'name' => 'Academic Support Staff',
            'type' => User::TYPE_USER,
        ]);

        $user = User::factory()->user()->create([
            'email' => 'tech.officer@example.com',
        ]);

        Http::fake([
            config('constants.department_data.base_url') . '/people/v1/staff/all/' => Http::response([
                [
                    'email' => 'tech.officer@example.com',
                    'designation' => 'Technical Officer',
                ],
            ]),
            config('constants.department_data.base_url') . '/people/v1/students/all/' => Http::response([]),
        ]);

        $listener = new UserEventListener();
        $listener->onCreated(new UserCreated($user));

        $this->assertTrue($user->hasRole('Academic Support Staff'));
    }

    /** @test */
    public function it_assigns_academic_support_staff_role_for_senior_technical_officer()
    {
        Cache::flush();

        Role::factory()->create([
            'name' => 'Academic Support Staff',
            'type' => User::TYPE_USER,
        ]);

        $user = User::factory()->user()->create([
            'email' => 'senior.tech@example.com',
        ]);

        Http::fake([
            config('constants.department_data.base_url') . '/people/v1/staff/all/' => Http::response([
                [
                    'email' => 'senior.tech@example.com',
                    'designation' => 'Senior Technical Officer',
                ],
            ]),
            config('constants.department_data.base_url') . '/people/v1/students/all/' => Http::response([]),
        ]);

        $listener = new UserEventListener();
        $listener->onCreated(new UserCreated($user));

        $this->assertTrue($user->hasRole('Academic Support Staff'));
    }
}
