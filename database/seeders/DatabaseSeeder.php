<?php

namespace Database\Seeders;

use Database\Seeders\Roles\AcademicRoleSeeder;
use Database\Seeders\Roles\EditorRoleSeeder;
use Database\Seeders\Roles\TaxonomyRoleSeeder;
use Database\Seeders\Roles\UserTypeRoleSeeder;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

/**
 * Class DatabaseSeeder.
 */
class DatabaseSeeder extends Seeder
{
  use TruncateTable;

  /**
   * Seed the application's database.
   */
  public function run()
  {
    Model::unguard();

    $this->truncateMultiple([
      'activity_log',
      'failed_jobs',
    ]);

    $this->call(AuthSeeder::class);

    // Roles and permissions
    $this->call(EditorRoleSeeder::class);
    $this->call(AcademicRoleSeeder::class);
    $this->call(TaxonomyRoleSeeder::class);
    $this->call(UserTypeRoleSeeder::class);

    // This seed data are required to function the site correctly
    $this->call(TaxonomySeeder::class);

    if (App::environment('local', 'testing')) {
      // Sample data for local and testing environments
      $this->call(AnnouncementSeeder::class);
      $this->call(NewsSeeder::class);
      $this->call(EventSeeder::class);
      $this->call(SemesterSeeder::class);
      $this->call(CourseSeeder::class);
      $this->call(TaxonomyFileSeeder::class);
    }

    Model::reguard();
  }
}