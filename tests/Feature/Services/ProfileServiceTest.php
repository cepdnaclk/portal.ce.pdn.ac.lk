<?php

namespace Tests\Feature\Services;

use App\Domains\Auth\Models\User;
use App\Domains\Profile\Models\UserProfile;
use App\Domains\Profile\Models\UserProfileType;
use App\Domains\Profile\Services\ProfileService;
use App\Exceptions\GeneralException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileServiceTest extends TestCase
{
  use RefreshDatabase;

  protected ProfileService $service;

  public function setUp(): void
  {
    parent::setUp();
    $this->service = app(ProfileService::class);
  }

  /** @test */
  public function it_stores_a_profile_with_types_and_links()
  {
    $profile = $this->service->store([
      'email' => 'jane@example.com',
      'full_name' => 'Jane Doe',
      'types' => [
        ['type' => UserProfileType::TYPE_STUDENT, 'attributes' => ['reg_number' => 'E/99/999', 'batch' => '2099']],
      ],
      'links' => [
        ['type' => 'github', 'url' => 'https://github.com/jane'],
      ],
    ]);

    $this->assertEquals('jane@example.com', $profile->email);
    $this->assertCount(1, $profile->profileTypes);
    $this->assertEquals('E/99/999', $profile->profileTypes->first()->attributes['reg_number']);
    $this->assertCount(1, $profile->links);
  }

  /** @test */
  public function it_rejects_a_duplicate_email_on_store()
  {
    UserProfile::factory()->create(['email' => 'jane@example.com']);

    $this->expectException(GeneralException::class);

    $this->service->store(['email' => 'jane@example.com']);
  }

  /** @test */
  public function update_syncs_links_deleting_missing_and_upserting_by_type()
  {
    $profile = $this->service->store([
      'email' => 'jane@example.com',
      'links' => [
        ['type' => 'github', 'url' => 'https://github.com/old'],
        ['type' => 'linkedin', 'url' => 'https://linkedin.com/in/jane'],
      ],
    ]);

    $profile = $this->service->update($profile, [
      'location' => 'Peradeniya',
      'links' => [
        ['type' => 'github', 'url' => 'https://github.com/new'],
        ['type' => 'website', 'url' => 'https://jane.dev'],
      ],
    ]);

    $this->assertEquals('Peradeniya', $profile->location);
    $this->assertEquals(
      ['github' => 'https://github.com/new', 'website' => 'https://jane.dev'],
      $profile->links->pluck('url', 'type')->all()
    );
  }

  /** @test */
  public function update_without_links_key_leaves_links_untouched()
  {
    $profile = $this->service->store([
      'email' => 'jane@example.com',
      'links' => [['type' => 'github', 'url' => 'https://github.com/jane']],
    ]);

    $profile = $this->service->update($profile, ['full_name' => 'Jane D.']);

    $this->assertCount(1, $profile->links);
  }

  /** @test */
  public function add_type_enforces_valid_types_and_is_idempotent()
  {
    $profile = UserProfile::factory()->create();

    $this->service->addType($profile, UserProfileType::TYPE_ACADEMIC_STAFF, ['designation' => 'Lecturer']);
    $this->service->addType($profile, UserProfileType::TYPE_ACADEMIC_STAFF, ['designation' => 'Professor']);

    $this->assertCount(1, $profile->refresh()->profileTypes);
    $this->assertEquals('Professor', $profile->profileTypes->first()->attributes['designation']);

    $this->expectException(GeneralException::class);
    $this->service->addType($profile, 'ALIEN');
  }

  /** @test */
  public function remove_type_removes_only_that_type()
  {
    $profile = UserProfile::factory()->create();
    $this->service->addType($profile, UserProfileType::TYPE_STUDENT);
    $this->service->addType($profile, UserProfileType::TYPE_ACADEMIC_STAFF);

    $this->service->removeType($profile, UserProfileType::TYPE_STUDENT);

    $this->assertEquals(
      [UserProfileType::TYPE_ACADEMIC_STAFF],
      $profile->refresh()->profileTypes->pluck('type')->all()
    );
  }

  /** @test */
  public function delete_soft_deletes_the_profile()
  {
    $profile = UserProfile::factory()->create();

    $this->service->delete($profile);

    $this->assertSoftDeleted('user_profiles', ['id' => $profile->id]);
  }

  /** @test */
  public function find_or_create_for_user_creates_a_minimal_linked_profile()
  {
    $user = User::factory()->user()->create();

    $profile = $this->service->findOrCreateForUser($user);

    $this->assertEquals($user->email, $profile->email);
    $this->assertEquals($user->name, $profile->full_name);
    $this->assertEquals($user->id, $profile->user_id);
    $this->assertCount(0, $profile->profileTypes);
  }

  /** @test */
  public function find_or_create_for_user_is_idempotent()
  {
    $user = User::factory()->user()->create();

    $first = $this->service->findOrCreateForUser($user);
    $second = $this->service->findOrCreateForUser($user->refresh());

    $this->assertTrue($first->is($second));
    $this->assertEquals(1, UserProfile::count());
  }

  /** @test */
  public function find_or_create_for_user_links_an_existing_profile_by_alternate_email()
  {
    $user = User::factory()->user()->create();
    $profile = UserProfile::factory()->create([
      'email' => 'faculty@eng.pdn.ac.lk',
      'alternate_email' => $user->email,
    ]);

    $found = $this->service->findOrCreateForUser($user);

    $this->assertTrue($found->is($profile));
    $this->assertEquals($user->id, $found->user_id);
    $this->assertEquals(1, UserProfile::count());
  }
}