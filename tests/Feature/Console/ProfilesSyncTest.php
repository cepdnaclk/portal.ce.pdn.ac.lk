<?php

namespace Tests\Feature\Console;

use App\Domains\Profile\Models\UserProfile;
use App\Domains\Profile\Models\UserProfileType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ProfilesSyncTest extends TestCase
{
  use RefreshDatabase;

  protected function fakeApi(array $students = [], array $staff = [], array $taxonomyStaff = []): void
  {
    Http::fake([
      config('constants.department_data.base_url') . '/people/v1/students/all/' => Http::response($students),
      config('constants.department_data.base_url') . '/people/v1/staff/all/' => Http::response($staff),
      rtrim(config('app.url'), '/') . '/api/taxonomy/v2/cepdnaclk/staff' => Http::response([
        'status' => 'success',
        'data' => ['terms' => $taxonomyStaff],
      ]),
    ]);
  }

  protected function studentRecord(array $overrides = []): array
  {
    return array_merge([
      'eNumber' => 'E/20/100',
      'batch' => '20',
      'department' => 'Computer Engineering',
      'current_affiliation' => 'University of Peradeniya',
      'honorific' => 'Mr',
      'full_name' => 'John Doe',
      'name_with_initials' => 'J. Doe',
      'preferred_short_name' => 'John',
      'preferred_long_name' => 'John Doe',
      'emails' => [
        'personal' => ['name' => 'john', 'domain' => 'gmail.com'],
        'faculty' => ['name' => 'e20100', 'domain' => 'eng.pdn.ac.lk'],
      ],
      'location' => 'Kandy',
      'interests' => ['ML', 'Robotics'],
      'profile_image' => 'https://people.ce.pdn.ac.lk/img/e20100.jpg',
      'urls' => ['github' => 'https://github.com/john', 'cv' => ''],
      'profile_page' => 'https://people.ce.pdn.ac.lk/students/e20/100/',
    ], $overrides);
  }

  /** @test */
  public function it_creates_profiles_from_both_feeds_and_skips_records_without_email()
  {
    $this->fakeApi(
      [
        'E/20/100' => $this->studentRecord(),
        'E/15/999' => $this->studentRecord([
          'eNumber' => 'E/15/999',
          'emails' => [
            'personal' => ['name' => '', 'domain' => ''],
            'faculty' => ['name' => '', 'domain' => ''],
          ],
        ]),
      ],
      [
        'jane' => [
          'name' => 'Dr. Jane Smith',
          'designation' => 'Senior Lecturer',
          'email' => 'jane@eng.pdn.ac.lk',
          'profile_url' => 'https://people.ce.pdn.ac.lk/staff/jane/',
          'profile_image' => 'https://people.ce.pdn.ac.lk/img/jane.jpg',
          'urls' => ['website' => 'https://jane.dev'],
          'research_interests' => ['AI'],
        ],
      ]
    );

    $this->artisan('profiles:sync')
      ->expectsOutput('Students: created=1, updated=0, linked=0, skipped_no_email=1, failed=0')
      ->assertExitCode(0);

    $student = UserProfile::where('email', 'e20100@eng.pdn.ac.lk')->firstOrFail();
    $this->assertEquals('john@gmail.com', $student->alternate_email);
    $this->assertEquals('John Doe', $student->full_name);

    $studentType = $student->profileTypes->firstWhere('type', UserProfileType::TYPE_STUDENT);
    $this->assertEquals('E/20/100', $studentType->source_key);
    $this->assertEquals('E/20/100', $studentType->getAttribute('attributes')['reg_number']);
    $this->assertEquals(['ML', 'Robotics'], $studentType->getAttribute('attributes')['interests']);

    // Non-empty urls only
    $this->assertEquals(['github'], $student->links->pluck('type')->all());

    $staff = UserProfile::where('email', 'jane@eng.pdn.ac.lk')->firstOrFail();
    $this->assertEquals('Senior Lecturer', $staff->current_position);
    $staffType = $staff->profileTypes->firstWhere('type', UserProfileType::TYPE_ACADEMIC_STAFF);
    $this->assertEquals('jane', $staffType->source_key);

    $this->assertEquals(2, UserProfile::count());
  }

  /** @test */
  public function rerunning_the_sync_converges_instead_of_duplicating()
  {
    $this->fakeApi(['E/20/100' => $this->studentRecord()]);

    $this->artisan('profiles:sync')->assertExitCode(0);
    $this->artisan('profiles:sync')
      ->expectsOutput('Students: created=0, updated=1, linked=0, skipped_no_email=0, failed=0')
      ->assertExitCode(0);

    $this->assertEquals(1, UserProfile::count());
    $this->assertEquals(1, UserProfileType::count());
  }

  /** @test */
  public function a_person_in_both_feeds_gets_one_profile_with_two_types()
  {
    // Alum now staff: staff email matches the student's faculty email
    $this->fakeApi(
      ['E/20/100' => $this->studentRecord()],
      [
        'john' => [
          'name' => 'Dr. John Doe',
          'designation' => 'Lecturer',
          'email' => 'e20100@eng.pdn.ac.lk',
          'profile_image' => '',
          'urls' => [],
          'research_interests' => [],
        ],
      ]
    );

    $this->artisan('profiles:sync')->assertExitCode(0);

    $this->assertEquals(1, UserProfile::count());
    $profile = UserProfile::firstOrFail();
    $this->assertEqualsCanonicalizing(
      [UserProfileType::TYPE_STUDENT, UserProfileType::TYPE_ACADEMIC_STAFF],
      $profile->profileTypes->pluck('type')->all()
    );
  }

  /** @test */
  public function it_syncs_nested_staff_from_the_internal_taxonomy_api()
  {
    $this->fakeApi([], [], [
      [
        'code' => 'academic-staff',
        'name' => 'Academic Staff',
        'terms' => [
          [
            'code' => 'jane-smith',
            'name' => 'Dr. Jane Smith',
            'metadata' => [
              'email' => 'jane@eng.pdn.ac.lk',
              'designation' => 'Senior Lecturer',
              'joined_date' => '2020-01-01',
              'leave_date' => '2025-07-31',
              'url_linkedin' => 'https://linkedin.com/in/jane',
            ],
          ],
        ],
      ],
    ]);

    $this->artisan('profiles:sync --staff --staff-source=taxonomy')
      ->expectsOutput('Staff: created=1, updated=0, linked=0, skipped_no_email=0, failed=0')
      ->assertExitCode(0);

    $profile = UserProfile::where('email', 'jane@eng.pdn.ac.lk')->firstOrFail();
    $attributes = $profile->profileTypes->first()->getAttribute('attributes');

    $this->assertEquals('Dr. Jane Smith', $profile->full_name);
    $this->assertEquals('Senior Lecturer', $profile->current_position);
    $this->assertEquals('jane-smith', $profile->profileTypes->first()->source_key);
    $this->assertEquals('2020-01-01', $attributes['start_date']);
    $this->assertEquals('2025-07-31', $attributes['end_date']);
    $this->assertEquals('https://linkedin.com/in/jane', $profile->links->firstWhere('type', 'linkedin')->url);

    Http::assertNotSent(function ($request) {
      return $request->url() === config('constants.department_data.base_url') . '/people/v1/staff/all/';
    });
  }

  /** @test */
  public function it_adopts_an_existing_unlinked_profile_by_email()
  {
    // e.g. created earlier by the user-creation listener
    $existing = UserProfile::factory()->create(['email' => 'e20100@eng.pdn.ac.lk']);

    $this->fakeApi(['E/20/100' => $this->studentRecord()]);

    $this->artisan('profiles:sync')
      ->expectsOutput('Students: created=0, updated=0, linked=1, skipped_no_email=0, failed=0')
      ->assertExitCode(0);

    $this->assertEquals(1, UserProfile::count());
    $type = $existing->refresh()->profileTypes->firstWhere('type', UserProfileType::TYPE_STUDENT);
    $this->assertEquals('E/20/100', $type->source_key);
  }

  /** @test */
  public function an_empty_api_value_never_overwrites_a_filled_field_and_links_are_not_deleted()
  {
    $this->fakeApi(['E/20/100' => $this->studentRecord()]);
    $this->artisan('profiles:sync')->assertExitCode(0);

    $profile = UserProfile::firstOrFail();
    $profile->links()->create(['type' => 'linkedin', 'url' => 'https://linkedin.com/in/john']);

    // Same record, but upstream lost the name and urls
    $this->fakeApi(['E/20/100' => $this->studentRecord(['full_name' => '', 'urls' => []])]);
    $this->artisan('profiles:sync')->assertExitCode(0);

    $profile->refresh();
    $this->assertEquals('John Doe', $profile->full_name);
    $this->assertEqualsCanonicalizing(['github', 'linkedin'], $profile->links->pluck('type')->all());
  }

  /** @test */
  public function dry_run_reports_counts_and_rolls_back()
  {
    $this->fakeApi(['E/20/100' => $this->studentRecord()]);

    $this->artisan('profiles:sync --dry-run')
      ->expectsOutput('Dry run — all changes rolled back.')
      ->expectsOutput('Students: created=1, updated=0, linked=0, skipped_no_email=0, failed=0')
      ->assertExitCode(0);

    $this->assertEquals(0, UserProfile::count());
  }

  /** @test */
  public function a_failed_fetch_aborts_with_an_error()
  {
    Http::fake([
      config('constants.department_data.base_url') . '/people/v1/students/all/' => Http::response(null, 500),
    ]);

    $this->artisan('profiles:sync --students')->assertExitCode(1);
    $this->assertEquals(0, UserProfile::count());
  }
}
