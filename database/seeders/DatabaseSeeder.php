<?php

namespace Database\Seeders;

use Database\Seeders\Roles\AcademicRoleSeeder;
use Database\Seeders\Roles\EditorRoleSeeder;
use Database\Seeders\Roles\TaxonomyRoleSeeder;
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


        if (App::environment('local', 'testing')) {
            $this->call(AuthSeeder::class);
            $this->call(AnnouncementSeeder::class);
            $this->call(NewsSeeder::class);
            $this->call(EventSeeder::class);
            $this->call(SemesterSeeder::class);
            $this->call(CourseSeeder::class);
            $this->call(TaxonomySeeder::class);
            $this->call(TaxonomyTermSeeder::class);
        }

        $this->call(EditorRoleSeeder::class);
        $this->call(AcademicRoleSeeder::class);
        $this->call(TaxonomyRoleSeeder::class);

        Model::reguard();
    }
}