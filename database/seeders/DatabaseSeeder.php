<?php

namespace Database\Seeders;

use Database\Seeders\Roles\AcademicRoleSeeder;
use Database\Seeders\Roles\EditorRoleSeeder;
use Database\Seeders\Roles\TaxonomyRoleSeeder;
use Database\Seeders\Traits\TruncateTable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;
// Ensure AuthSeeder is available if it's not already (it should be in the same namespace)
// use Database\Seeders\AuthSeeder; // Usually not needed if in same namespace, but good for clarity

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


        if (App::environment('local', 'testing')) {
            // Test data seeders - AuthSeeder was called here
            $this->call(AuthSeeder::class); // This contains UserSeeder, PermissionRoleSeeder, UserRoleSeeder, and cache clearing
            $this->call(AnnouncementSeeder::class);
            $this->call(NewsSeeder::class);
            $this->call(EventSeeder::class);
            $this->call(SemesterSeeder::class);
            $this->call(CourseSeeder::class);
            $this->call(TaxonomySeeder::class); // Model factory seeder
            $this->call(TaxonomyTermSeeder::class); // Model factory seeder
        }

        // User permission seeders - these were called after the testing/local block
        // This was the order that caused issues, which is what we are reverting to.
        $this->call(EditorRoleSeeder::class);
        $this->call(AcademicRoleSeeder::class);
        $this->call(TaxonomyRoleSeeder::class);

        Model::reguard();
    }
}
