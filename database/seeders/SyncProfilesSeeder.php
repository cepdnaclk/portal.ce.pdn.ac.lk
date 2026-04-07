<?php

namespace Database\Seeders;

use App\Domains\Profiles\Models\Profile;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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
        $this->command?->error("Profile sync failed for {$source['kind']}: {$e->getMessage()}");
        $logger->error('Profile sync source failed.', ['kind' => $source['kind'], 'error' => $e->getMessage()]);
        continue;
      }

      foreach ((array) $records as $record) {
        try {
          $payload = $this->mapPayload($record, $source['type']);

          if (! $payload['email'] || ! $payload['type']) {
            throw new \RuntimeException('Missing required email or type after payload mapping.');
          }

          Profile::updateOrCreate(
            ['email' => $payload['email'], 'type' => $payload['type']],
            $payload
          );
        } catch (\Throwable $e) {
          $identifier = Arr::get($record, 'email', Arr::get($record, 'id', 'unknown'));
          $this->command?->error("Profile sync failed for {$identifier}: {$e->getMessage()}");
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

  protected function mapPayload(array $record, ?string $defaultType): array
  {
    $email = mb_strtolower((string) (Arr::get($record, 'email')
      ?: Arr::get($record, 'emails.0')
      ?: Arr::get($record, 'official_email')
      ?: Arr::get($record, 'personal_email')));

    return [
      'email' => $email,
      'type' => $defaultType ?: $this->resolveStaffType($record),
      'full_name' => Arr::get($record, 'full_name', Arr::get($record, 'name')),
      'name_with_initials' => Arr::get($record, 'name_with_initials'),
      'preferred_short_name' => Arr::get($record, 'preferred_short_name'),
      'preferred_long_name' => Arr::get($record, 'preferred_long_name'),
      'honorific' => in_array(Arr::get($record, 'honorific', ''), Profile::HONORIFICS, true) ? Arr::get($record, 'honorific', '') : '',
      'reg_no' => Arr::get($record, 'reg_no', Arr::get($record, 'registration_number')),
      'profile_picture' => Arr::get($record, 'profile_picture'),
      'current_position' => Arr::get($record, 'current_position', Arr::get($record, 'position')),
      'department' => Arr::get($record, 'department'),
      'phone_number' => Arr::get($record, 'phone_number', Arr::get($record, 'phone')),
      'personal_email' => Arr::get($record, 'personal_email'),
      'office_email' => Arr::get($record, 'office_email', Arr::get($record, 'email')),
      'resident_address' => Arr::get($record, 'resident_address'),
      'current_location' => Arr::get($record, 'current_location'),
      'current_affiliation' => $this->mapCurrentAffiliation($record),
      'previous_affiliations' => $this->mapPreviousAffiliations($record),
      'biography' => Arr::get($record, 'biography', Arr::get($record, 'bio')),
      'profile_url' => Arr::get($record, 'profile_url'),
      'profile_api' => Arr::get($record, 'profile_api'),
      'profile_website' => Arr::get($record, 'website'),
      'profile_cv' => Arr::get($record, 'cv'),
      'profile_linkedin' => Arr::get($record, 'linkedin'),
      'profile_github' => Arr::get($record, 'github'),
      'profile_researchgate' => Arr::get($record, 'researchgate'),
      'profile_google_scholar' => Arr::get($record, 'google_scholar'),
      'profile_orcid' => Arr::get($record, 'orcid'),
      'profile_facebook' => Arr::get($record, 'facebook'),
      'profile_twitter' => Arr::get($record, 'twitter'),
      'review_status' => Profile::REVIEW_STATUS_APPROVED,
    ];
  }

  protected function resolveStaffType(array $record): string
  {
    $role = Arr::get($record, 'role', Arr::get($record, 'staff_type', ''));
    $normalized = mb_strtolower((string) $role);

    if (str_contains($normalized, 'temporary')) {
      return Profile::TYPE_TEMPORARY_ACADEMIC_STAFF;
    }

    if (str_contains($normalized, 'support')) {
      return Profile::TYPE_ACADEMIC_SUPPORT;
    }

    if (str_contains($normalized, 'external')) {
      return Profile::TYPE_EXTERNAL;
    }

    return Profile::TYPE_ACADEMIC_STAFF;
  }

  protected function mapCurrentAffiliation(array $record): ?array
  {
    $affiliation = Arr::get($record, 'current_affiliation.affiliation', Arr::get($record, 'department'));
    $startDate = Arr::get($record, 'current_affiliation.start_date', Arr::get($record, 'start_date'));

    if (! $affiliation && ! $startDate) {
      return null;
    }

    return [
      'affiliation' => $affiliation,
      'start_date' => $startDate,
    ];
  }

  protected function mapPreviousAffiliations(array $record): array
  {
    return collect(Arr::get($record, 'previous_affiliations', []))
      ->map(fn($item) => [
        'affiliation' => Arr::get($item, 'affiliation'),
        'start_date' => Arr::get($item, 'start_date'),
        'end_date' => Arr::get($item, 'end_date'),
      ])
      ->filter(fn($item) => array_filter($item))
      ->values()
      ->all();
  }
}
