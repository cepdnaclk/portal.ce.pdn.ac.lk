<?php

namespace Tests\Unit\Domains\Profile;

use App\Domains\Profile\Models\UserProfile;
use App\Domains\Profile\Models\UserProfileLink;
use App\Domains\Profile\Models\UserProfileType;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserProfileTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function a_profile_cannot_have_the_same_type_twice()
  {
    $profile = UserProfile::factory()->create();
    UserProfileType::factory()->create(['user_profile_id' => $profile->id]);

    $this->expectException(QueryException::class);

    UserProfileType::factory()->create(['user_profile_id' => $profile->id]);
  }

  /** @test */
  public function an_empty_profile_has_zero_completeness()
  {
    $profile = UserProfile::factory()->create(['full_name' => null]);

    $this->assertEquals(0, $profile->completeness);
    $this->assertFalse($profile->isComplete());
  }

  /** @test */
  public function a_half_filled_profile_is_complete()
  {
    $profile = UserProfile::factory()->create([
      'full_name' => 'Jane Doe',
      'name_with_initials' => 'J. Doe',
      'preferred_short_name' => 'Jane',
      'preferred_long_name' => 'Jane Doe',
      'honorific' => 'Ms',
      'location' => 'Peradeniya',
    ]);

    // 6 of 10 base checks filled
    $this->assertGreaterThanOrEqual(50, $profile->completeness);
    $this->assertTrue($profile->isComplete());
  }

  /** @test */
  public function completeness_counts_required_type_attributes_and_links()
  {
    $profile = UserProfile::factory()->create(['full_name' => null]);
    UserProfileType::factory()->create([
      'user_profile_id' => $profile->id,
      'type' => UserProfileType::TYPE_STUDENT,
      'attributes' => ['reg_number' => 'E/20/100', 'batch' => null],
    ]);
    UserProfileLink::factory()->create(['user_profile_id' => $profile->id]);

    // 12 checks: 10 base + reg_number + batch; filled = link + reg_number
    $this->assertEquals((int) round(2 / 12 * 100), $profile->completeness);
  }

  /** @test */
  public function for_email_scope_matches_primary_and_alternate_email()
  {
    $profile = UserProfile::factory()->create([
      'email' => 'primary@example.com',
      'alternate_email' => 'alternate@example.com',
    ]);

    $this->assertTrue(UserProfile::forEmail('primary@example.com')->first()->is($profile));
    $this->assertTrue(UserProfile::forEmail('alternate@example.com')->first()->is($profile));
    $this->assertNull(UserProfile::forEmail('other@example.com')->first());
  }

  /** @test */
  public function it_resolves_uploaded_and_external_profile_image_urls()
  {
    $fileName = '00000000-0000-4000-8000-000000000000.jpg';
    $profile = new UserProfile(['profile_image' => $fileName]);

    $this->assertEquals(
      route('download.profile-image', ['fileName' => $fileName]),
      $profile->profileImageUrl()
    );

    $profile->profile_image = 'https://people.ce.pdn.ac.lk/images/person.jpg';

    $this->assertEquals($profile->profile_image, $profile->profileImageUrl());
  }
}
