<?php

namespace Database\Seeders\Auth;

use App\Domains\Auth\Models\User;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

/**
 * Class UserTableSeeder.
 */
class UserSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seed.
     */
    public function run()
    {
        $this->disableForeignKeys();

        // Add the master administrator, user id of 1
        User::create([
            'type' => User::TYPE_ADMIN,
            'name' => 'Super Admin',
            'email' => env('SEED_ADMIN_EMAIL', 'admin@portal.ce.pdn.ac.lk'),
            'password' => env('SEED_ADMIN_PASSWORD', 'admin_user'),
            'email_verified_at' => now(),
            'active' => true,
        ]);

        // Only for the local testings
        if (app()->environment(['local', 'testing'])) {
            User::create([
                'type' => User::TYPE_USER,
                'name' => 'Test User',
                'email' => env('SEED_USER_EMAIL', 'user@portal.ce.pdn.ac.lk'),
                'password' => env('SEED_USER_PASSWORD', 'regular_user'),
                'email_verified_at' => now(),
                'active' => true,
            ]);

            User::create([
                'type' => User::TYPE_USER,
                'name' => 'NewsEditor1',
                'email' => env('SEED_NEWS_EDITOR_EMAIL', 'news.editor@portal.ce.pdn.ac.lk'),
                'password' => env('SEED_NEWS_EDITOR_PASSWORD', 'news'),
                'email_verified_at' => now(),
                'active' => true,
            ]);

            User::create([
                'type' => User::TYPE_USER,
                'name' => 'EventEditor1',
                'email' => env('SEED_EVENT_EDITOR_EMAIL', 'events.editor@portal.ce.pdn.ac.lk'),
                'password' => env('SEED_EVENT_EDITOR_PASSWORD', 'events'),
                'email_verified_at' => now(),
                'active' => true,
            ]);

            User::create([
                'type' => User::TYPE_USER,
                'name' => 'CourseManager',
                'email' => env('SEED_COURSE_MANAGER_EMAIL', 'course_manager@portal.ce.pdn.ac.lk'),
                'password' => env('SEED_COURSE_MANAGER_PASSWORD', 'course_manager'),
                'email_verified_at' => now(),
                'active' => true,
            ]);
        }

        $this->enableForeignKeys();
    }
}
