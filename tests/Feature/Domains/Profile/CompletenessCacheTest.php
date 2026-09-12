<?php

namespace Tests\Feature\Domains\Profile;

use App\Domains\Profile\Models\UserProfile;
use App\Domains\Profile\Models\UserProfileLink;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompletenessCacheTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function adding_a_link_invalidates_the_cached_completeness(): void
  {
    $profile = UserProfile::factory()->create();

    $before = $profile->completeness;

    UserProfileLink::create([
      'user_profile_id' => $profile->id,
      'type' => 'website',
      'url' => 'https://example.com',
    ]);

    $this->assertGreaterThan($before, $profile->fresh()->completeness);
  }
}
