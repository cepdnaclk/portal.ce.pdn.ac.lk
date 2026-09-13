<?php

namespace Tests\Feature\Domains\Profile;

use App\Domains\Auth\Models\User;
use App\Domains\Profile\Models\UserProfile;
use App\Domains\Profile\Services\ProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileUserSyncTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function updating_the_preferred_long_name_renames_the_linked_user()
  {
    $user = User::factory()->user()->create(['name' => 'Old Name']);
    $profile = UserProfile::factory()->create(['email' => $user->email, 'user_id' => $user->id]);

    app(ProfileService::class)->update($profile, ['preferred_long_name' => 'Janet A. Doe']);

    $this->assertEquals('Janet A. Doe', $user->refresh()->name);
  }

  /** @test */
  public function the_user_avatar_falls_back_to_gravatar_and_prefers_the_profile_picture()
  {
    $user = User::factory()->user()->create();
    $this->assertStringContainsString('gravatar.com', $user->avatar);

    UserProfile::factory()->create([
      'email' => $user->email,
      'user_id' => $user->id,
      'profile_image' => 'https://cdn.example.com/me.jpg',
    ]);

    $this->assertEquals('https://cdn.example.com/me.jpg', $user->fresh()->avatar);
  }

  /** @test */
  public function an_unlinked_profile_update_changes_no_user()
  {
    $user = User::factory()->user()->create(['name' => 'Old Name']);
    $profile = UserProfile::factory()->create(['user_id' => null]);

    app(ProfileService::class)->update($profile, ['preferred_long_name' => 'Janet A. Doe']);

    $this->assertEquals('Old Name', $user->refresh()->name);
  }
}
