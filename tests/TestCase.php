<?php

namespace Tests;

use App\Domains\Auth\Http\Middleware\TwoFactorAuthenticationStatus;
use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\User;
use Illuminate\Auth\Middleware\RequirePassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

/**
 * Class TestCase.
 */
abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        Artisan::call('db:seed');

        $this->withoutMiddleware(RequirePassword::class);
        $this->withoutMiddleware(TwoFactorAuthenticationStatus::class);
    }

    protected function getAdminRole()
    {
        return Role::find(1);
    }

    protected function getMasterAdmin()
    {
        return User::find(1);
    }

    protected function loginAsAdmin($admin = false)
    {
        if (! $admin) {
            $admin = $this->getMasterAdmin();
        }

        $this->actingAs($admin);

        return $admin;
    }


    protected function loginAsEditor()
    {
        $newsEditorRole = Role::where('name', 'Editor')->first();
        $user = User::factory()->admin()->create(['name' => 'Test Editor']);
        $user->assignRole($newsEditorRole->name);
        $this->actingAs($user);

        return $user;
    }

    protected function loginAsCourseManager()
    {
        $courseManagerRole = Role::where('name', 'Course Manager')->first();
        $user = User::factory()->user()->create(['name' => 'Test Course Manager']);
        $user->assignRole($courseManagerRole->name);
        $this->actingAs($user);

        return $user;
    }


    protected function logout()
    {
        return auth()->logout();
    }
}
