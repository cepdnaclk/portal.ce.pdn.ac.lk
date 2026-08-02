<?php

namespace Tests\Feature\Domains\Profile;

use App\Domains\Auth\Events\User\UserLoggedIn;
use App\Domains\Auth\Listeners\UserEventListener;
use App\Domains\Auth\Models\User;
use App\Domains\Auth\Services\UserService;
use App\Domains\Profile\Models\UserProfile;
use App\Domains\Profile\Services\ProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProfileAutoLinkTest extends TestCase
{
  use RefreshDatabase;

  public function setUp(): void
  {
    parent::setUp();
    Cache::flush();
    Http::fake([
      config('constants.department_data.base_url') . '/people/v1/staff/all/' => Http::response([]),
      config('constants.department_data.base_url') . '/people/v1/students/all/' => Http::response([]),
    ]);
  }

  /** @test */
  public function creating_a_user_results_in_a_linked_profile()
  {
    $user = app(UserService::class)->registerUser([
      'name' => 'Jane Doe',
      'email' => 'jane@example.com',
      'password' => 'secret-password',
    ]);

    $profile = $user->refresh()->profile;

    $this->assertNotNull($profile);
    $this->assertEquals('jane@example.com', $profile->email);
  }

  /** @test */
  public function creating_a_user_links_an_existing_unlinked_profile_instead_of_duplicating()
  {
    $profile = UserProfile::factory()->create(['email' => 'jane@example.com']);

    $user = app(UserService::class)->registerUser([
      'name' => 'Jane Doe',
      'email' => 'jane@example.com',
      'password' => 'secret-password',
    ]);

    $this->assertTrue($user->refresh()->profile->is($profile));
    $this->assertEquals(1, UserProfile::count());
  }

  /** @test */
  public function a_failing_profile_creation_does_not_break_user_creation()
  {
    $this->mock(ProfileService::class, function ($mock) {
      $mock->shouldReceive('findOrCreateForUser')->andThrow(new \RuntimeException('boom'));
    });

    $user = app(UserService::class)->registerUser([
      'name' => 'Jane Doe',
      'email' => 'jane@example.com',
      'password' => 'secret-password',
    ]);

    $this->assertDatabaseHas('users', ['email' => 'jane@example.com']);
    $this->assertNull($user->refresh()->profile);
  }

  /** @test */
  public function first_login_creates_a_profile_for_a_pre_existing_user()
  {
    $user = User::factory()->user()->create();
    $this->assertNull($user->profile);

    (new UserEventListener())->onLoggedIn(new UserLoggedIn($user));

    $this->assertNotNull($user->refresh()->profile);
  }
}