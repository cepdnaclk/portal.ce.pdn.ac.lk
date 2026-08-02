<?php

namespace Tests\Feature\Backend;

use App\Domains\Auth\Models\User;
use App\Domains\Profile\Models\UserProfile;
use App\Domains\Profile\Models\UserProfileType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
  use RefreshDatabase;

  protected function loginWithPermission(?string $permission = null): User
  {
    $user = User::factory()->user()->create();

    if ($permission) {
      $user->givePermissionTo($permission);
    }

    $this->actingAs($user);

    return $user;
  }

  // Permission failures redirect home with a flash message (see Exceptions\Handler)
  protected function assertDenied($response): void
  {
    $response->assertStatus(302)->assertSessionHas('flash_danger');
  }

  /** @test */
  public function unauthorized_users_cannot_access_profiles()
  {
    $this->loginWithPermission();

    $this->assertDenied($this->get(route('dashboard.profiles.index')));
    $this->assertDenied($this->get(route('dashboard.profiles.create')));
  }

  /** @test */
  public function a_viewer_can_see_the_index_but_cannot_edit()
  {
    $this->loginWithPermission('user.access.profiles.viewer');
    $profile = UserProfile::factory()->create();

    $this->get(route('dashboard.profiles.index'))->assertOk();
    $this->get(route('dashboard.profiles.show', $profile))->assertOk();

    $this->assertDenied($this->get(route('dashboard.profiles.create')));
    $this->assertDenied($this->post(route('dashboard.profiles.store'), ['email' => 'x@example.com']));
    $this->assertDenied($this->get(route('dashboard.profiles.edit', $profile)));
    $this->assertDenied($this->put(route('dashboard.profiles.update', $profile), ['email' => $profile->email]));
    $this->assertDenied($this->delete(route('dashboard.profiles.destroy', $profile)));

    $this->assertEquals(1, UserProfile::count());
    $this->assertNull($profile->refresh()->deleted_at);
  }

  /** @test */
  public function an_editor_can_create_a_profile_with_types_and_links()
  {
    $this->loginWithPermission('user.access.profiles.editor');

    $this->get(route('dashboard.profiles.create'))->assertOk();

    $response = $this->post(route('dashboard.profiles.store'), [
      'email' => 'new@example.com',
      'full_name' => 'New Person',
      'types' => [
        'STUDENT' => [
          'assigned' => '1',
          'attributes' => ['reg_number' => 'E/20/123', 'batch' => '2020', 'interests' => 'ml, systems'],
        ],
      ],
      'links' => ['github' => 'https://github.com/new'],
    ]);

    $response->assertRedirect(route('dashboard.profiles.index'));

    $profile = UserProfile::where('email', 'new@example.com')->firstOrFail();
    $this->assertEquals(['STUDENT'], $profile->profileTypes->pluck('type')->all());
    $this->assertEquals(['ml', 'systems'], $profile->profileTypes->first()->attributes['interests']);
    $this->assertEquals('https://github.com/new', $profile->links->firstWhere('type', 'github')->url);
  }

  /** @test */
  public function an_editor_can_update_and_delete_a_profile()
  {
    $this->loginWithPermission('user.access.profiles.editor');
    $profile = UserProfile::factory()->create();

    $this->get(route('dashboard.profiles.edit', $profile))->assertOk();

    $this->put(route('dashboard.profiles.update', $profile), [
      'email' => $profile->email,
      'location' => 'Peradeniya',
    ])->assertRedirect(route('dashboard.profiles.index'));

    $this->assertEquals('Peradeniya', $profile->refresh()->location);

    $this->delete(route('dashboard.profiles.destroy', $profile))
      ->assertRedirect(route('dashboard.profiles.index'));

    $this->assertSoftDeleted('user_profiles', ['id' => $profile->id]);
  }

  /** @test */
  public function student_type_requires_a_valid_reg_number_and_batch()
  {
    $this->loginWithPermission('user.access.profiles.editor');

    $response = $this->post(route('dashboard.profiles.store'), [
      'email' => 'student@example.com',
      'types' => [
        'STUDENT' => ['assigned' => '1', 'attributes' => ['reg_number' => 'nope']],
      ],
    ]);

    $response->assertSessionHasErrors([
      'types.STUDENT.attributes.reg_number',
      'types.STUDENT.attributes.batch',
    ]);
    $this->assertEquals(0, UserProfile::count());
  }

  /** @test */
  public function academic_staff_dates_are_optional_and_must_be_in_order()
  {
    $this->loginWithPermission('user.access.profiles.editor');

    $this->post(route('dashboard.profiles.store'), [
      'email' => 'academic@example.com',
      'types' => [
        'ACADEMIC_STAFF' => [
          'assigned' => '1',
          'attributes' => [
            'designation' => 'Lecturer',
            'end_date' => '2025-07-31',
          ],
        ],
      ],
    ])->assertRedirect(route('dashboard.profiles.index'));

    $profile = UserProfile::where('email', 'academic@example.com')->firstOrFail();
    $attributes = $profile->profileTypes->first()->getAttribute('attributes');
    $this->assertArrayNotHasKey('start_date', $attributes);
    $this->assertEquals('2025-07-31', $attributes['end_date']);

    $this->post(route('dashboard.profiles.store'), [
      'email' => 'invalid-dates@example.com',
      'types' => [
        'ACADEMIC_STAFF' => [
          'assigned' => '1',
          'attributes' => [
            'designation' => 'Professor',
            'start_date' => '2025-01-01',
            'end_date' => '2024-12-31',
          ],
        ],
      ],
    ])->assertSessionHasErrors('types.ACADEMIC_STAFF.attributes.end_date');
  }

  /** @test */
  public function invalid_profile_and_link_types_are_rejected()
  {
    $this->loginWithPermission('user.access.profiles.editor');

    $this->post(route('dashboard.profiles.store'), [
      'email' => 'invalid@example.com',
      'types' => ['ALIEN' => ['assigned' => '1']],
      'links' => ['myspace' => 'https://myspace.com/x'],
    ])->assertSessionHasErrors(['types', 'links']);

    $this->assertEquals(0, UserProfile::count());
  }

  /** @test */
  public function a_duplicate_email_is_rejected()
  {
    $this->loginWithPermission('user.access.profiles.editor');
    UserProfile::factory()->create(['email' => 'taken@example.com']);

    $this->post(route('dashboard.profiles.store'), ['email' => 'taken@example.com'])
      ->assertSessionHasErrors(['email']);
  }

  /** @test */
  public function an_editor_can_upload_a_profile_picture()
  {
    Storage::fake('public');
    $this->loginWithPermission('user.access.profiles.editor');

    $this->post(route('dashboard.profiles.store'), [
      'email' => 'picture@example.com',
      'profile_image' => UploadedFile::fake()->image('picture.jpg', 400, 400),
    ])->assertRedirect(route('dashboard.profiles.index'));

    $profile = UserProfile::where('email', 'picture@example.com')->firstOrFail();
    $path = 'profile-images/' . $profile->profile_image;

    $this->assertEquals(
      route('download.profile-image', ['fileName' => $profile->profile_image]),
      $profile->profileImageUrl()
    );
    Storage::disk('public')->assertExists($path);
    $this->get($profile->profileImageUrl())
      ->assertOk()
      ->assertHeader('Content-Type', 'image/jpeg');
  }
}
