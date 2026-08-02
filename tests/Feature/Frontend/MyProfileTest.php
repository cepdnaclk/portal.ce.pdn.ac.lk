<?php

namespace Tests\Feature\Frontend;

use App\Domains\Auth\Models\User;
use App\Domains\Profile\Models\UserProfile;
use App\Domains\Profile\Models\UserProfileType;
use App\Domains\Profile\Services\ProfileService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class MyProfileTest extends TestCase
{
  use RefreshDatabase;

  /** @test */
  public function a_user_without_a_profile_gets_one_created_on_first_visit()
  {
    $user = User::factory()->user()->create();
    $this->actingAs($user);

    $this->assertNull($user->profile);

    $this->get(route('intranet.user.profile.manage'))->assertOk();

    $this->assertNotNull($user->refresh()->profile);
  }

  /** @test */
  public function a_user_can_update_own_names_links_and_type_attributes()
  {
    $user = User::factory()->user()->create();
    $this->actingAs($user);

    $profile = app(ProfileService::class)->findOrCreateForUser($user);
    app(ProfileService::class)->addType($profile, UserProfileType::TYPE_STUDENT, [
      'reg_number' => 'E/20/100',
      'batch' => '2020',
      'interests' => ['old'],
    ]);

    $response = $this->patch(route('intranet.user.profile.manage.update'), [
      'full_name' => 'Updated Name',
      'location' => 'Kandy',
      'links' => ['github' => 'https://github.com/me'],
      'types' => [
        'STUDENT' => [
          'attributes' => [
            'interests' => 'ml, robotics',
            'reg_number' => 'E/99/999', // must be ignored
          ],
        ],
      ],
    ]);

    $response->assertRedirect(route('intranet.user.profile.manage'));

    $profile->refresh();
    $this->assertEquals('Updated Name', $profile->full_name);
    $this->assertEquals('Kandy', $profile->location);
    $this->assertEquals('https://github.com/me', $profile->links->firstWhere('type', 'github')->url);

    $attributes = $profile->profileTypes->first()->getAttribute('attributes');
    $this->assertEquals(['ml', 'robotics'], $attributes['interests']);
    $this->assertEquals('E/20/100', $attributes['reg_number']);
    $this->assertEquals('2020', $attributes['batch']);
  }

  /** @test */
  public function submitted_email_and_type_changes_are_ignored()
  {
    $user = User::factory()->user()->create();
    $this->actingAs($user);

    $profile = app(ProfileService::class)->findOrCreateForUser($user);
    $originalEmail = $profile->email;

    $this->patch(route('intranet.user.profile.manage.update'), [
      'email' => 'hijack@example.com',
      'full_name' => 'Still Me',
      'types' => [
        'ACADEMIC_STAFF' => ['assigned' => '1', 'attributes' => ['designation' => 'Professor']],
      ],
    ])->assertRedirect(route('intranet.user.profile.manage'));

    $profile->refresh();
    $this->assertEquals($originalEmail, $profile->email);
    $this->assertEquals('Still Me', $profile->full_name);
    $this->assertCount(0, $profile->profileTypes);
  }

  /** @test */
  public function a_user_can_update_academic_staff_dates()
  {
    $user = User::factory()->user()->create();
    $this->actingAs($user);

    $profile = app(ProfileService::class)->findOrCreateForUser($user);
    app(ProfileService::class)->addType($profile, UserProfileType::TYPE_ACADEMIC_STAFF, [
      'designation' => 'Lecturer',
    ]);
    $profile->load('profileTypes');

    $this->patch(route('intranet.user.profile.manage.update'), [
      'types' => [
        'ACADEMIC_STAFF' => [
          'attributes' => [
            'start_date' => '2020-01-01',
            'end_date' => '2025-07-31',
          ],
        ],
      ],
    ])->assertRedirect(route('intranet.user.profile.manage'));

    $attributes = $profile->refresh()->profileTypes->first()->getAttribute('attributes');
    $this->assertEquals('2020-01-01', $attributes['start_date']);
    $this->assertEquals('2025-07-31', $attributes['end_date']);
  }

  /** @test */
  public function a_user_cannot_touch_another_users_profile()
  {
    $other = UserProfile::factory()->create(['full_name' => 'Someone Else']);

    $user = User::factory()->user()->create();
    $this->actingAs($user);

    $this->patch(route('intranet.user.profile.manage.update'), [
      'full_name' => 'Mine',
    ])->assertRedirect(route('intranet.user.profile.manage'));

    $this->assertEquals('Someone Else', $other->refresh()->full_name);
    $this->assertEquals('Mine', $user->refresh()->profile->full_name);
  }

  /** @test */
  public function a_user_can_upload_a_profile_picture()
  {
    Storage::fake('public');
    $user = User::factory()->user()->create();
    $this->actingAs($user);

    $this->patch(route('intranet.user.profile.manage.update'), [
      'profile_image' => UploadedFile::fake()->image('portrait.jpg', 400, 400),
    ])->assertRedirect(route('intranet.user.profile.manage'));

    $profile = $user->refresh()->profile;
    $oldPath = 'profile-images/' . $profile->profile_image;

    $this->assertEquals(
      route('download.profile-image', ['fileName' => $profile->profile_image]),
      $profile->profileImageUrl()
    );
    Storage::disk('public')->assertExists($oldPath);

    $this->patch(route('intranet.user.profile.manage.update'), [
      'profile_image' => UploadedFile::fake()->image('replacement.jpg', 500, 500),
    ])->assertRedirect(route('intranet.user.profile.manage'));

    $newPath = 'profile-images/' . $profile->refresh()->profile_image;

    Storage::disk('public')->assertMissing($oldPath);
    Storage::disk('public')->assertExists($newPath);
  }
}
