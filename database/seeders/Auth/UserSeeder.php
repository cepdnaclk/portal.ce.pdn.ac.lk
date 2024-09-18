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
                'name' => 'User',
                'email' => env('SEED_USER_EMAIL', 'user@portal.ce.pdn.ac.lk'),
                'password' => env('SEED_USER_PASSWORD', 'regular_user'),
                'email_verified_at' => now(),
                'active' => true,
            ]);

            User::create([
                'type' => User::TYPE_USER,
                'name' => 'News Editor',
                'email' => env('SEED_NEWS_EDITOR_EMAIL', 'user+news.editor@portal.ce.pdn.ac.lk'),
                'password' => env('SEED_NEWS_EDITOR_PASSWORD', 'news'),
                'email_verified_at' => now(),
                'active' => true,
            ]);

            User::create([
                'type' => User::TYPE_USER,
                'name' => 'Event Editor',
                'email' => env('SEED_EVENT_EDITOR_EMAIL', 'user+events.editor@portal.ce.pdn.ac.lk'),
                'password' => env('SEED_EVENT_EDITOR_PASSWORD', 'events'),
                'email_verified_at' => now(),
                'active' => true,
            ]);
        }

        $this->enableForeignKeys();
    }
}
