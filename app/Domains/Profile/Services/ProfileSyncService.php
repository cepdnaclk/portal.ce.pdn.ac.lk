<?php

namespace App\Domains\Profile\Services;

use App\Domains\Profile\Models\UserProfile;
use App\Domains\Profile\Models\UserProfileLink;
use App\Domains\Profile\Models\UserProfileType;
use Illuminate\Support\Facades\Log;

/**
 * Class ProfileSyncService.
 *
 * Idempotent upserts from the department People API (see
 * docs/features/profiles-implementation-plan.md, "Migration & sync plan").
 * Identity resolution order: (type, source_key), then email, then create.
 * Profile scalars are overwritten with non-empty API values only; links are
 * upserted by type and never deleted; only the synced type's attributes are
 * touched.
 */
class ProfileSyncService
{
  /**
   * Sync the students feed (records keyed by eNumber).
   *
   * @return array counts: created/updated/linked/skipped_no_email/failed
   */
  public function syncStudents(array $records): array
  {
    $counts = $this->emptyCounts();

    foreach ($records as $eNumber => $record) {
      $facultyEmail = $this->buildEmail($record['emails']['faculty'] ?? []);
      $personalEmail = $this->buildEmail($record['emails']['personal'] ?? []);
      $email = $facultyEmail ?? $personalEmail;

      if (! $email) {
        // A profile that can never be claimed is dead data — skip, re-runs converge
        $counts['skipped_no_email']++;
        continue;
      }

      $sourceKey = (string) ($record['eNumber'] ?? $eNumber);

      $this->syncRecord($counts, UserProfileType::TYPE_STUDENT, $sourceKey, $email, [
        'email' => $email,
        'alternate_email' => $facultyEmail ? $personalEmail : null,
        'honorific' => $record['honorific'] ?? null,
        'full_name' => $record['full_name'] ?? null,
        'name_with_initials' => $record['name_with_initials'] ?? null,
        'preferred_short_name' => $record['preferred_short_name'] ?? null,
        'preferred_long_name' => $record['preferred_long_name'] ?? null,
        'location' => $record['location'] ?? null,
        'current_affiliation' => $record['current_affiliation'] ?? null,
        'profile_image' => $record['profile_image'] ?? null,
      ], [
        'reg_number' => $sourceKey,
        'batch' => $record['batch'] ?? null,
        'department' => $record['department'] ?? null,
        'interests' => $record['interests'] ?? [],
      ], $record['urls'] ?? []);
    }

    return $counts;
  }

  /**
   * Sync the staff feed (records keyed by API username).
   *
   * @return array counts: created/updated/linked/skipped_no_email/failed
   */
  public function syncStaff(array $records): array
  {
    $counts = $this->emptyCounts();

    foreach ($records as $username => $record) {
      $email = $record['email'] ?? null;

      if (blank($email)) {
        $counts['skipped_no_email']++;
        continue;
      }

      $this->syncRecord($counts, UserProfileType::TYPE_ACADEMIC_STAFF, (string) $username, $email, [
        'email' => $email,
        'full_name' => $record['name'] ?? null,
        'current_position' => $record['designation'] ?? null,
        'profile_image' => $record['profile_image'] ?? null,
      ], [
        'designation' => $record['designation'] ?? null,
        'research_interests' => $record['research_interests'] ?? [],
      ], $record['urls'] ?? []);
    }

    return $counts;
  }

  private function syncRecord(array &$counts, string $type, string $sourceKey, string $email, array $values, array $attributes, array $urls): void
  {
    try {
      // 1. Primary idempotency key — survives upstream email changes
      $profile = UserProfileType::where('type', $type)
        ->where('source_key', $sourceKey)
        ->first()?->profile;
      $status = 'updated';

      // 2. Adopt an existing profile by email (e.g. created by the user-creation listener)
      if (! $profile) {
        $profile = UserProfile::forEmail($email)->first()
          ?? (filled($values['alternate_email'] ?? null) ? UserProfile::forEmail($values['alternate_email'])->first() : null);
        $status = $profile ? 'linked' : 'created';
      }

      // 3. No match — create
      $profile ??= new UserProfile();

      // Overwrite with API values, but never a non-null field with an empty one
      $profile->fill(array_filter($values, fn($value) => filled($value)))->save();

      $profile->profileTypes()->updateOrCreate(
        ['type' => $type],
        ['attributes' => array_filter($attributes, fn($value) => filled($value)), 'source_key' => $sourceKey]
      );

      // Upsert known link types only; never delete existing links
      foreach (array_filter($urls) as $linkType => $url) {
        if (in_array($linkType, UserProfileLink::LINK_TYPES, true)) {
          $profile->links()->updateOrCreate(['type' => $linkType], ['url' => $url]);
        }
      }

      $counts[$status]++;
    } catch (\Throwable $e) {
      // One bad record must not abort a cron sync
      $counts['failed']++;
      Log::warning('profiles:sync failed for record', [
        'type' => $type,
        'source_key' => $sourceKey,
        'error' => $e->getMessage(),
      ]);
    }
  }

  private function buildEmail(array $parts): ?string
  {
    $name = $parts['name'] ?? '';
    $domain = $parts['domain'] ?? '';

    return ($name !== '' && $domain !== '') ? "$name@$domain" : null;
  }

  private function emptyCounts(): array
  {
    return ['created' => 0, 'updated' => 0, 'linked' => 0, 'skipped_no_email' => 0, 'failed' => 0];
  }
}
