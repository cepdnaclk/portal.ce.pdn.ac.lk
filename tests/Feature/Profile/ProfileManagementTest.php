<?php

namespace Tests\Feature\Profile;

use App\Domains\Auth\Models\Permission;
use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\User;
use App\Domains\Profiles\Models\Profile;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

class ProfileManagementTest extends TestCase
{
  /** @test */
  public function a_profile_manager_can_view_the_profile_management_page()
  {
    $user = User::factory()->user()->create();
    $user->assignRole('Profile Manager');
    $this->actingAs($user);

    $this->get(route('dashboard.profiles.index'))->assertOk();
  }

  /** @test */
  public function a_user_can_create_their_allowed_profile_type()
  {
    $user = User::factory()->user()->create([
      'email' => 'student@eng.pdn.ac.lk',
    ]);
    $user->assignRole('Student');

    $this->actingAs($user)
      ->post(route('dashboard.my-profiles.store'), [
        'type' => Profile::TYPE_UNDERGRADUATE_STUDENT,
        'email' => $user->email,
        'full_name' => 'Student One',
        'gender' => Profile::GENDER_FEMALE,
        'civil_status' => Profile::CIVIL_STATUS_SINGLE,
        'honorific' => 'Dr.',
        'reg_no' => 'E/24/001',
        'phone_number' => '0123456789',
        'biography' => 'Bio',
        'current_position' => 'Student',
        'department' => 'Computer Engineering',
      ])
      ->assertRedirect(route('dashboard.my-profiles.index'));

    $this->assertDatabaseHas('profiles', [
      'email' => $user->email,
      'user_id' => $user->id,
      'type' => Profile::TYPE_UNDERGRADUATE_STUDENT,
      'gender' => Profile::GENDER_FEMALE,
      'civil_status' => Profile::CIVIL_STATUS_SINGLE,
      'honorific' => 'Dr.',
      'reg_no' => 'E/24/001',
      'review_status' => Profile::REVIEW_STATUS_APPROVED,
    ]);
  }

  /** @test */
  public function registration_number_must_match_the_expected_format()
  {
    $user = User::factory()->user()->create([
      'email' => 'student2@eng.pdn.ac.lk',
    ]);
    $user->assignRole('Student');

    $this->actingAs($user)
      ->from(route('dashboard.my-profiles.create', ['type' => Profile::TYPE_UNDERGRADUATE_STUDENT]))
      ->post(route('dashboard.my-profiles.store'), [
        'type' => Profile::TYPE_UNDERGRADUATE_STUDENT,
        'email' => $user->email,
        'full_name' => 'Student Two',
        'reg_no' => '2024-001',
        'phone_number' => '0123456789',
        'biography' => 'Bio',
        'current_position' => 'Student',
        'department' => 'Computer Engineering',
      ])
      ->assertRedirect(route('dashboard.my-profiles.create', ['type' => Profile::TYPE_UNDERGRADUATE_STUDENT]))
      ->assertSessionHasErrors('reg_no');
  }

  /** @test */
  public function linked_profiles_are_shown_on_the_account_page()
  {
    $user = User::factory()->user()->create();
    Profile::factory()->create([
      'user_id' => $user->id,
      'email' => $user->email,
      'type' => Profile::TYPE_ACADEMIC_STAFF,
      'full_name' => 'Dr Example',
      'preferred_long_name' => 'Dr Example',
    ]);

    $this->actingAs($user)
      ->get('/intranet/account')
      ->assertSee('Linked Profiles')
      ->assertSee('Dr Example')
      ->assertSee('Academic Staff');
  }

  /** @test */
  public function shared_identity_fields_sync_across_profiles_for_the_same_user()
  {
    $user = User::factory()->user()->create(['email' => 'multi@ce.pdn.ac.lk']);
    $first = Profile::factory()->create([
      'user_id' => $user->id,
      'email' => $user->email,
      'type' => Profile::TYPE_ACADEMIC_STAFF,
      'full_name' => 'Old Name',
    ]);
    $second = Profile::factory()->create([
      'user_id' => $user->id,
      'email' => $user->email,
      'type' => Profile::TYPE_EXTERNAL,
      'full_name' => 'Old Name',
    ]);

    $user->assignRole('External Collaborator');
    $user->assignRole('Lecturer');

    $this->actingAs($user)
      ->patch(route('dashboard.my-profiles.update', $first), [
        'type' => $first->type,
        'email' => $user->email,
        'full_name' => 'New Shared Name',
        'preferred_long_name' => 'New Portal Display Name',
        'gender' => Profile::GENDER_FEMALE,
        'civil_status' => Profile::CIVIL_STATUS_MARRIED,
        'honorific' => 'Eng.',
        'reg_no' => 'E/23/123',
        'phone_number' => '0771234567',
        'biography' => 'Updated biography',
        'current_position' => 'Lecturer',
        'department' => 'Computer Engineering',
      ])
      ->assertRedirect(route('dashboard.my-profiles.index'));

    $this->assertDatabaseHas('profiles', [
      'id' => $second->id,
      'full_name' => 'New Shared Name',
      'preferred_long_name' => 'New Portal Display Name',
      'gender' => Profile::GENDER_FEMALE,
      'civil_status' => Profile::CIVIL_STATUS_MARRIED,
      'honorific' => 'Eng.',
    ]);

    $this->assertDatabaseHas('users', [
      'id' => $user->id,
      'name' => 'New Portal Display Name',
    ]);
  }

  /** @test */
  public function profile_title_is_resolved_from_honorific_gender_and_civil_status()
  {
    $professional = Profile::factory()->make([
      'gender' => Profile::GENDER_FEMALE,
      'civil_status' => Profile::CIVIL_STATUS_MARRIED,
      'honorific' => 'Eng.',
    ]);
    $singleFemale = Profile::factory()->make([
      'gender' => Profile::GENDER_FEMALE,
      'civil_status' => Profile::CIVIL_STATUS_SINGLE,
      'honorific' => '',
    ]);
    $marriedFemale = Profile::factory()->make([
      'gender' => Profile::GENDER_FEMALE,
      'civil_status' => Profile::CIVIL_STATUS_MARRIED,
      'honorific' => '',
    ]);
    $widowedFemale = Profile::factory()->make([
      'gender' => Profile::GENDER_FEMALE,
      'civil_status' => Profile::CIVIL_STATUS_WIDOWED,
      'honorific' => '',
    ]);
    $male = Profile::factory()->make([
      'gender' => Profile::GENDER_MALE,
      'civil_status' => '',
      'honorific' => '',
    ]);

    $this->assertSame('Eng.', $professional->title);
    $this->assertSame('Miss.', $singleFemale->title);
    $this->assertSame('Mrs.', $marriedFemale->title);
    $this->assertSame('Ms.', $widowedFemale->title);
    $this->assertSame('Mr.', $male->title);
  }

  /** @test */
  public function profile_email_must_use_an_eng_or_ce_domain()
  {
    $user = User::factory()->user()->create([
      'email' => 'student@eng.pdn.ac.lk',
    ]);
    $user->assignRole('Student');

    $this->actingAs($user)
      ->from(route('dashboard.my-profiles.create', ['type' => Profile::TYPE_UNDERGRADUATE_STUDENT]))
      ->post(route('dashboard.my-profiles.store'), [
        'type' => Profile::TYPE_UNDERGRADUATE_STUDENT,
        'email' => 'student@example.com',
        'full_name' => 'Student Three',
        'reg_no' => 'E/24/003',
        'phone_number' => '0123456789',
        'biography' => 'Bio',
        'current_position' => 'Student',
        'department' => 'Computer Engineering',
      ])
      ->assertRedirect(route('dashboard.my-profiles.create', ['type' => Profile::TYPE_UNDERGRADUATE_STUDENT]))
      ->assertSessionHasErrors('email');
  }

  /** @test */
  public function a_profile_manager_can_view_delete_confirmation_and_delete_a_profile()
  {
    $user = User::factory()->user()->create();
    $user->assignRole('Profile Manager');
    $profile = Profile::factory()->create([
      'full_name' => 'Delete Me',
    ]);

    $this->actingAs($user)
      ->get(route('dashboard.profiles.delete', $profile))
      ->assertOk()
      ->assertSee('Delete Me');

    $this->actingAs($user)
      ->delete(route('dashboard.profiles.destroy', $profile))
      ->assertRedirect(route('dashboard.profiles.index'));

    $this->assertDatabaseMissing('profiles', [
      'id' => $profile->id,
    ]);
  }
}
