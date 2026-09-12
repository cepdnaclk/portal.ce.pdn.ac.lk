<?php

namespace App\Console\Commands;

use App\Domains\Profile\Services\ProfileSyncService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class ProfilesSync extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'profiles:sync
    {--students : Sync only the students feed}
    {--staff : Sync only the staff feed}
    {--staff-source=both : Staff source: people, taxonomy, or both}
    {--dry-run : Run the full pass in a transaction, print counts, and roll back}
    {--details : List every created/updated/linked/skipped record, not just the counts}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Sync user profiles from the department People and internal Taxonomy APIs (idempotent, safe on cron). '
    . 'Overwrites synced fields with non-empty API values. '
    . 'NOTE: once student self-service replaces the API as source of truth, switch students to seed-only.';

  /**
   * Execute the console command.
   *
   * @return int
   */
  public function handle(ProfileSyncService $sync)
  {
    $students = $this->option('students') || ! $this->option('staff');
    $staff = $this->option('staff') || ! $this->option('students');
    $staffSource = (string) $this->option('staff-source');

    if (! in_array($staffSource, ['people', 'taxonomy', 'both'], true)) {
      $this->error('The --staff-source option must be people, taxonomy, or both.');

      return self::FAILURE;
    }

    $run = function () use ($sync, $students, $staff, $staffSource) {
      $results = [];

      if ($students) {
        $results['students'] = $sync->syncStudents($this->fetch(
          config('constants.department_data.base_url'),
          '/people/v1/students/all/'
        ));
      }

      if ($staff) {
        $peopleStaff = in_array($staffSource, ['people', 'both'], true)
          ? $this->fetch(config('constants.department_data.base_url'), '/people/v1/staff/all/')
          : [];
        $taxonomyStaff = in_array($staffSource, ['taxonomy', 'both'], true)
          ? $this->taxonomyStaffRecords($this->fetch(config('app.url'), '/api/taxonomy/v2/cepdnaclk/staff'))
          : [];

        $results['staff'] = $sync->syncStaff($this->mergeStaffRecords($peopleStaff, $taxonomyStaff));
      }

      return $results;
    };

    try {
      if ($this->option('dry-run')) {
        DB::beginTransaction();

        try {
          $results = $run();
        } finally {
          DB::rollBack();
        }

        $this->warn('Dry run — all changes rolled back.');
      } else {
        $results = $run();
      }
    } catch (Throwable $e) {
      $this->error($e->getMessage());

      return self::FAILURE;
    }

    foreach ($results as $source => $counts) {
      $this->info(ucfirst($source) . ': ' . collect($counts)->map(fn($count, $key) => "$key=$count")->implode(', '));
    }

    $this->report($sync->events);

    return self::SUCCESS;
  }

  /**
   * Failures always; the full per-record breakdown only with --details.
   */
  private function report(array $events): void
  {
    $failed = array_filter($events, fn($event) => $event['status'] === 'failed');

    if ($this->option('details')) {
      $this->newLine();
      $this->table(
        ['Type', 'Source key', 'Email', 'Status', 'Error'],
        array_map(fn($event) => array_map(fn($value) => $value ?? '—', $event), $events)
      );
    }

    if ($failed && ! $this->option('details')) {
      $this->newLine();
      $this->error(count($failed) . ' record(s) failed:');
      foreach ($failed as $event) {
        $this->line("  {$event['type']} {$event['source_key']} ({$event['email']}): {$event['error']}");
      }
    }
  }

  /**
   * Fresh fetch — deliberately bypasses the DepartmentDataService cache.
   */
  private function fetch(string $baseUrl, string $endpoint): array
  {
    $url = rtrim($baseUrl, '/') . $endpoint;
    $response = Http::get($url);

    if (! $response->successful()) {
      throw new \RuntimeException("Failed to fetch $url (HTTP {$response->status()})");
    }

    return $response->json() ?? [];
  }

  /**
   * Convert nested staff taxonomy terms to the shape consumed by ProfileSyncService.
   */
  private function taxonomyStaffRecords(array $response): array
  {
    $records = [];
    $walk = function (array $terms) use (&$walk, &$records) {
      foreach ($terms as $term) {
        $metadata = $term['metadata'] ?? [];

        // Also tolerate the raw [{code, value}] metadata shape.
        if (isset($metadata[0]) && is_array($metadata[0])) {
          $metadata = collect($metadata)->pluck('value', 'code')->all();
        }

        if (filled($metadata['email'] ?? null)) {
          $code = (string) ($term['code'] ?? $metadata['email']);
          $records[$code] = [
            'name' => $term['name'] ?? null,
            'email' => $metadata['email'],
            'designation' => $metadata['designation'] ?? null,
            'profile_image' => $metadata['profile_image'] ?? null,
            'start_date' => $metadata['joined_date'] ?? null,
            'end_date' => $metadata['leave_date'] ?? null,
            'urls' => array_filter([
              'linkedin' => $metadata['url_linkedin'] ?? null,
              'website' => $metadata['url_profile'] ?? null,
            ]),
          ];
        }

        $walk((array) ($term['terms'] ?? []));
      }
    };

    $walk((array) data_get($response, 'data.terms', []));

    return $records;
  }

  /**
   * Merge taxonomy details into People API records, matching the same person by email.
   */
  private function mergeStaffRecords(array $peopleStaff, array $taxonomyStaff): array
  {
    foreach ($taxonomyStaff as $taxonomyKey => $taxonomyRecord) {
      $email = strtolower($taxonomyRecord['email']);
      $peopleKey = collect($peopleStaff)->search(function ($record) use ($email) {
        return strtolower((string) ($record['email'] ?? '')) === $email;
      });
      $key = $peopleKey === false ? $taxonomyKey : $peopleKey;
      $record = array_filter($taxonomyRecord, fn($value) => filled($value));

      $peopleStaff[$key] = array_replace_recursive($peopleStaff[$key] ?? [], $record);
    }

    return $peopleStaff;
  }
}
