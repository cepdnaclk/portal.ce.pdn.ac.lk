<?php

namespace Database\Seeders;

use App\Domains\Profiles\Models\Profile;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Domains\Auth\Models\User;

class SyncProfilesSeeder extends Seeder
{
  use DisableForeignKeys;

  public function run()
  {
    $logger = Log::build([
      'driver' => 'single',
      'path' => storage_path('logs/profile-sync.log'),
    ]);

    $sources = [
      [
        'url' => config('profiles.sync.students_url'),
        'type' => Profile::TYPE_UNDERGRADUATE_STUDENT,
        'kind' => 'students',
      ],
      [
        'url' => config('profiles.sync.staff_url'),
        'type' => null,
        'kind' => 'staff',
      ],
    ];

    foreach ($sources as $source) {
      try {
        $response = Http::timeout(config('profiles.sync.timeout'))->get($source['url']);
        $response->throw();
        $records = $response->json();
      } catch (\Throwable $e) {
        $this->command?->error(">> Profile sync failed for {$source['kind']}: {$e->getMessage()}");
        $logger->error('Profile sync source failed.', ['kind' => $source['kind'], 'error' => $e->getMessage()]);
        continue;
      }

      foreach ((array) $records as $key => $record) {
        try {
          $payload = $this->mapPayload($record, $source['type']);

          if (! $payload['email'] || ! $payload['type']) {
            throw new \RuntimeException('Missing required email or type after payload mapping.');
          }
          $this->command?->info(">> Syncing profile: {$payload['email']} ({$payload['type']})");
          Profile::updateOrCreate(
            ['email' => $payload['email'], 'type' => $payload['type']],
            $payload
          );
        } catch (\Throwable $e) {
          $identifier = Arr::get($record, 'email') ?? Arr::get($record, 'eNumber');
          $this->command?->error(">> Profile sync failed for {$identifier}: {$e->getMessage()}");
          $logger->error('Profile sync record failed.', [
            'kind' => $source['kind'],
            'identifier' => $identifier,
            'error' => $e->getMessage(),
            'record' => $record,
          ]);
        }
      }
    }
  }

  protected function resolveHonorific(array $record): ?string
  {
    $display_name = Arr::get($record, 'name');

    if (str_contains($display_name, 'Dr.')) {
      return  'Dr.';
    } elseif (str_contains($display_name, 'Prof.')) {
      return  'Prof.';
    }
    return  null;
  }

  protected function resolveType(array $record, ?string $defaultType): ?string
  {
    // TODO Refactor the UNDERGRADUATE type to ALUMNI with a proper logic
    if ($defaultType) {
      return $defaultType;
    }

    $designation = Arr::get($record, 'designation');

    switch ($designation) {
      case 'Lecturers (Prob)':
      case 'Lecturer':
      case 'Lecturers': // TODO correct this later
      case 'Senior Lecturer':
      case 'Senior Lecturers': // TODO correct this laterå
      case 'Professor':
      case 'Staff Assistant':
        return Profile::TYPE_ACADEMIC_STAFF;

      case 'Temporary Instructor':
      case 'Temporary Lecturer':
        return Profile::TYPE_TEMPORARY_ACADEMIC_STAFF;

      case 'Staff Assistant':
      case 'Technical Officer':
      case 'Computer Operator':
        return Profile::TYPE_ACADEMIC_SUPPORT;

      case 'Visiting Research Fellow':
      default:
        return Profile::TYPE_EXTERNAL;
    }
  }

  protected function resolveEmail(array $record, string $type, ?string $emailType): ?string
  {
    switch ($type) {
      case Profile::TYPE_UNDERGRADUATE_STUDENT:
        // Note: Student Emails are stored in a nested structure, that need to be mapped properly.
        // For now, personal emails will be considered, if official emails are not available, but this logic can be adjusted as needed.
        $emailType = $emailType ? $emailType : 'faculty';
        $emailObj = Arr::get($record, 'emails')[$emailType];
        if ($emailObj['name'] && $emailObj['domain']) {
          return mb_strtolower((string) $emailObj['name']) . '@' . mb_strtolower((string) $emailObj['domain']);
        }
        return null;

      case Profile::TYPE_POSTGRADUATE_STUDENT:
      case Profile::TYPE_ACADEMIC_STAFF:
      case Profile::TYPE_TEMPORARY_ACADEMIC_STAFF:
      case Profile::TYPE_ACADEMIC_SUPPORT:
      case Profile::TYPE_EXTERNAL:
        $email = Arr::get($record, 'email');
        return $email ? mb_strtolower((string) $email) : null;
    }

    return null;
  }

  protected function resolveURL($url): ?string
  {
    return $url && Str::startsWith($url, ['http://', 'https://']) ? $url : null;
  }

  protected function syncProfilePicture(?string $imageUrl): ?string
  {
    if (! $imageUrl) {
      return null;
    }

    $disk = Storage::disk(config('profiles.image.disk'));
    $directory = trim((string) config('profiles.image.directory'), '/');
    $extension = $this->resolveProfileImageExtension($imageUrl);
    $path = $directory . '/' . sha1($imageUrl) . '.' . $extension;

    if ($disk->exists($path)) {
      return $path;
    }

    try {
      $response = Http::timeout(config('profiles.sync.timeout'))->get($imageUrl);
      $response->throw();
    } catch (\Throwable $e) {
      return null;
    }

    $disk->put($path, $response->body());

    return $path;
  }

  protected function resolveProfileImageExtension(string $imageUrl): string
  {
    $path = parse_url($imageUrl, PHP_URL_PATH);
    $extension = strtolower(pathinfo((string) $path, PATHINFO_EXTENSION));

    return in_array($extension, config('profiles.image.extensions', []), true) ? $extension : 'jpg';
  }

  protected function mapPayload(array $record, ?string $defaultType): array
  {
    $type = $this->resolveType($record, $defaultType);

    if ($type === Profile::TYPE_UNDERGRADUATE_STUDENT) {
      // For student API records
      $faculty_email = $this->resolveEmail($record, $type, 'faculty');
      $personal_email = $this->resolveEmail($record, $type, 'personal');
      $office_email = null; // Not applicable for students
      $regNo = ($type === Profile::TYPE_UNDERGRADUATE_STUDENT) ? Arr::get($record, 'eNumber') : null;
      $profileAPI = $regNo ?  "https://api.ce.pdn.ac.lk/people/v1/students/" . str_replace("E/", "E", $regNo) : null; // Refine later
      $current_position = 'Undergraduate'; // Refine later
      $current_affiliation = Arr::get($record, 'current_affiliation') ?? "";
      $honorific = null;
    } else {
      // For staff API records
      $faculty_email = $this->resolveEmail($record, $type, null);
      $personal_email = null;
      $office_email = null; // Refine later
      $regNo = null; // Not applicable for staff
      $profileAPI = null; // Refine later
      $current_position = Arr::get($record, 'designation');
      $current_affiliation = "Department of Computer Engineering, University of Peradeniya"; // Refine later (resignations)
      $honorific = $this->resolveHonorific($record);
    }

    $department = Arr::get($record, 'department') ?? "Computer Engineering";
    $urls = Arr::get($record, 'urls', []);
    $userId = User::where('email', $faculty_email ?: $personal_email)->value('id') ?? null;

    return [
      'user_id' => $userId,
      'type' => $type,
      // Personal Information
      'full_name' => Arr::get($record, 'full_name', Arr::get($record, 'name')),
      'name_with_initials' => Arr::get($record, 'name_with_initials'),
      'preferred_short_name' => Arr::get($record, 'preferred_short_name'),
      'preferred_long_name' => Arr::get($record, 'preferred_long_name'),
      'gender' => null,
      'civil_status' => null,
      'honorific' => $honorific,
      // Additional Personal Information
      'biography' => "",
      'profile_picture' => null, //$this->syncProfilePicture(Arr::get($record, 'profile_image')),
      'profile_cv' => Arr::get($urls, 'cv'),
      // Student Details
      'reg_no' =>  $regNo,
      'department' => $department,
      // Contact Information
      'email' => $faculty_email ?: $personal_email,
      'personal_email' => $personal_email,
      'office_email' => $office_email,
      'phone_number' => null,
      'resident_address' => null,
      'current_location' => Arr::get($record, 'location'),
      // Professional Information
      'current_position' => $current_position,
      'current_affiliation' => $current_affiliation,
      'previous_affiliations' => [],
      // Profile URLs
      'profile_api' => $profileAPI,
      'profile_url' => $this->resolveURL(Arr::get($record, 'profile_page')),
      'profile_website' => $this->resolveURL(Arr::get($urls, 'website')),
      'profile_linkedin' => $this->resolveURL(Arr::get($urls, 'linkedin')),
      'profile_github' => $this->resolveURL(Arr::get($urls, 'github')),
      'profile_researchgate' => $this->resolveURL(Arr::get($urls, 'researchgate')),
      'profile_google_scholar' => $this->resolveURL(Arr::get($urls, 'google_scholar')),
      'profile_orcid' => $this->resolveURL(Arr::get($urls, 'orcid')),
      'profile_facebook' => $this->resolveURL(Arr::get($urls, 'facebook')),
      'profile_twitter' => $this->resolveURL(Arr::get($urls, 'twitter')),
      // Audit Fields
      'review_status' => Profile::REVIEW_STATUS_APPROVED,
    ];
  }
}