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
  /** Upstream placeholder rows that must never become real profiles. */
  private const PLACEHOLDER_EMAILS = ['johndoe@eng.pdn.ac.lk'];

  /**
   * Per-record outcomes for the current run: type, source_key, email, status, error.
   * Appended to by every syncRecord() call; read by profiles:sync for its report.
   */
  public array $events = [];

  /** Optional callback fired as each record is processed, for live console output. */
  public $onEvent = null;

  public function __construct(private ProfileImageService $profileImageService)
  {
  }

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
        $this->record(UserProfileType::TYPE_STUDENT, (string) ($record['eNumber'] ?? $eNumber), null, 'skipped_no_email');
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
        'interests' => $record['interests'] ?? [],
      ], [
        'reg_number' => $sourceKey,
        'batch' => $record['batch'] ?? null,
        'department' => $this->mapDepartment($record['department'] ?? null, $sourceKey),
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
        $this->record(UserProfileType::TYPE_ACADEMIC_STAFF, (string) $username, null, 'skipped_no_email');
        continue;
      }

      $this->syncRecord($counts, UserProfileType::TYPE_ACADEMIC_STAFF, (string) $username, $email, [
        'email' => $email,
        'full_name' => $record['name'] ?? null,
        'current_position' => $record['designation'] ?? null,
        'profile_image' => $record['profile_image'] ?? null,
        'interests' => $record['research_interests'] ?? [],
      ], [
        'designation' => $record['designation'] ?? null,
        'start_date' => $record['start_date'] ?? null,
        'end_date' => $record['end_date'] ?? null,
      ], $record['urls'] ?? []);
    }

    return $counts;
  }

  /**
   * Map the API's raw department name to a UserProfile::DEPARTMENT_OPTIONS
   * key (see UserProfile::mapDepartment()). An unrecognised value is
   * dropped rather than stored: syncRecord() only overwrites with non-empty
   * values, so this just leaves any existing department untouched instead
   * of persisting a string that can never satisfy the Rule::in validation
   * or be selected in the admin dropdown. Logged so a new or renamed
   * upstream department is noticed rather than silently lost.
   */
  private function mapDepartment(?string $raw, string $sourceKey): ?string
  {
    if (blank($raw)) {
      return null;
    }

    $mapped = UserProfile::mapDepartment($raw);

    if ($mapped === null) {
      Log::warning('profiles:sync could not map department to a known option', [
        'source_key' => $sourceKey,
        'department' => $raw,
      ]);
    }

    return $mapped;
  }

  private function syncRecord(array &$counts, string $type, string $sourceKey, string $email, array $values, array $attributes, array $urls): void
  {
    if (in_array(strtolower($email), self::PLACEHOLDER_EMAILS, true)) {
      $counts['skipped_no_email']++;
      $this->record($type, $sourceKey, $email, 'skipped_placeholder');

      return;
    }

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

      // profile_image is a URL to download, not a column value — handled separately below
      $imageUrl = $values['profile_image'] ?? null;
      unset($values['profile_image']);

      // Overwrite with API values, but never a non-null field with an empty one
      $profile->fill(array_filter($values, fn($value) => filled($value)))->save();

      // A flaky/dead image URL shouldn't fail the whole record.
      // ponytail: re-downloads every run even if unchanged; add last-synced-URL
      // tracking to skip that only if bandwidth/CPU actually becomes a problem.
      if (filled($imageUrl)) {
        try {
          $this->profileImageService->replaceFromUrl($profile, $imageUrl);
        } catch (\Throwable $e) {
          Log::warning('profiles:sync failed to download profile image', [
            'profile_id' => $profile->id,
            'url' => $imageUrl,
            'error' => $e->getMessage(),
          ]);
        }
      }

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
      $this->record($type, $sourceKey, $email, $status);
    } catch (\Throwable $e) {
      // One bad record must not abort a cron sync
      $counts['failed']++;
      $this->record($type, $sourceKey, $email, 'failed', $e->getMessage());
      Log::warning('profiles:sync failed for record', [
        'type' => $type,
        'source_key' => $sourceKey,
        'error' => $e->getMessage(),
      ]);
    }
  }


  private function record(string $type, string $sourceKey, ?string $email, string $status, ?string $error = null): void
  {
    $event = [
      'type' => $type,
      'source_key' => $sourceKey,
      'email' => $email,
      'status' => $status,
      'error' => $error,
    ];

    $this->events[] = $event;

    if ($this->onEvent) {
      ($this->onEvent)($event);
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
