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
    {--dry-run : Run the full pass in a transaction, print counts, and roll back}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Sync user profiles from the department People API (idempotent, safe on cron). '
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

    $run = function () use ($sync, $students, $staff) {
      $results = [];

      if ($students) {
        $results['students'] = $sync->syncStudents($this->fetch('/people/v1/students/all/'));
      }

      if ($staff) {
        $results['staff'] = $sync->syncStaff($this->fetch('/people/v1/staff/all/'));
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

    return self::SUCCESS;
  }

  /**
   * Fresh fetch — deliberately bypasses the DepartmentDataService cache.
   */
  private function fetch(string $endpoint): array
  {
    $url = config('constants.department_data.base_url') . $endpoint;
    $response = Http::get($url);

    if (! $response->successful()) {
      throw new \RuntimeException("Failed to fetch $url (HTTP {$response->status()})");
    }

    return $response->json() ?? [];
  }
}
