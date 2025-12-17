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

class AssignStudentRoleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_assigns_student_role_based_on_department_email()
    {
        Cache::flush();

        Role::factory()->create([
            'name' => 'Student',
            'type' => User::TYPE_USER,
        ]);

        $user = User::factory()->user()->create([
            'email' => 'student@eng.pdn.ac.lk',
        ]);

        Http::fake([
            config('constants.department_data.base_url') . '/people/v1/staff/all/' => Http::response([]),
            config('constants.department_data.base_url') . '/people/v1/students/all/' => Http::response([
                [
                    'name' => 'John Doe',
                    'emails' => [
                        'faculty' => [
                            'name' => 'student',
                            'domain' => 'eng.pdn.ac.lk',
                        ],
                        'personal' => [
                            'name' => '',
                            'domain' => '',
                        ],
                    ],
                ],
            ]),
        ]);

        $listener = new UserEventListener();
        $listener->onCreated(new UserCreated($user));

        $this->assertTrue($user->hasRole('Student'));
    }

    /** @test */
    public function it_assigns_student_role_for_personal_email()
    {
        Cache::flush();

        Role::factory()->create([
            'name' => 'Student',
            'type' => User::TYPE_USER,
        ]);

        $user = User::factory()->user()->create([
            'email' => 'personal@eng.pdn.ac.lk',
        ]);

        Http::fake([
            config('constants.department_data.base_url') . '/people/v1/staff/all/' => Http::response([]),
            config('constants.department_data.base_url') . '/people/v1/students/all/' => Http::response([
                [
                    'name' => 'Jane Doe',
                    'emails' => [
                        'faculty' => [
                            'name' => '',
                            'domain' => '',
                        ],
                        'personal' => [
                            'name' => 'personal',
                            'domain' => 'eng.pdn.ac.lk',
                        ],
                    ],
                ],
            ]),
        ]);

        $listener = new UserEventListener();
        $listener->onCreated(new UserCreated($user));

        $this->assertTrue($user->hasRole('Student'));
    }

    /** @test */
    public function it_does_not_assign_student_role_for_non_student_email()
    {
        Cache::flush();

        Role::factory()->create([
            'name' => 'Student',
            'type' => User::TYPE_USER,
        ]);

        $user = User::factory()->user()->create([
            'email' => 'other@example.com',
        ]);

        Http::fake([
            config('constants.department_data.base_url') . '/people/v1/staff/all/' => Http::response([]),
            config('constants.department_data.base_url') . '/people/v1/students/all/' => Http::response([
                [
                    'name' => 'Some Student',
                    'emails' => [
                        'faculty' => [
                            'name' => 'student',
                            'domain' => 'eng.pdn.ac.lk',
                        ],
                        'personal' => [
                            'name' => '',
                            'domain' => '',
                        ],
                    ],
                ],
            ]),
        ]);

        $listener = new UserEventListener();
        $listener->onCreated(new UserCreated($user));

        $this->assertFalse($user->hasRole('Student'));
    }
}
