<?php

namespace Database\Seeders\Auth;

use App\Domains\Auth\Models\Permission;
use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\User;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

/**
 * Class PermissionRoleTableSeeder.
 */
class PermissionRoleSeeder extends Seeder
{
    use DisableForeignKeys;

    /**
     * Run the database seed.
     */
    public function run()
    {
        $this->disableForeignKeys();

        // Create Roles
        Role::create([
            'id' => 1,
            'type' => User::TYPE_ADMIN,
            'name' => 'Administrator',
        ]);

        Role::create([
            'id' => 2, 
            'type' => User::TYPE_ADMIN,
            'name' => 'News Editor', 
        ]);

        Role::create([
            'id' => 3, 
            'type' => User::TYPE_ADMIN,
            'name' => 'Event Editor', 
        ]);

        // Non Grouped Permissions
        //

        // Grouped permissions
        // Users category
        $users = Permission::create([
            'type' => User::TYPE_ADMIN,
            'name' => 'admin.access.user',
            'description' => 'All User Permissions',
        ]);

        $users->children()->saveMany([
            new Permission([
                'type' => User::TYPE_ADMIN,
                'name' => 'admin.access.user.list',
                'description' => 'View Users',
            ]),
            new Permission([
                'type' => User::TYPE_ADMIN,
                'name' => 'admin.access.user.deactivate',
                'description' => 'Deactivate Users',
                'sort' => 2,
            ]),
            new Permission([
                'type' => User::TYPE_ADMIN,
                'name' => 'admin.access.user.reactivate',
                'description' => 'Reactivate Users',
                'sort' => 3,
            ]),
            new Permission([
                'type' => User::TYPE_ADMIN,
                'name' => 'admin.access.user.clear-session',
                'description' => 'Clear User Sessions',
                'sort' => 4,
            ]),
            new Permission([
                'type' => User::TYPE_ADMIN,
                'name' => 'admin.access.user.impersonate',
                'description' => 'Impersonate Users',
                'sort' => 5,
            ]),
            new Permission([
                'type' => User::TYPE_ADMIN,
                'name' => 'admin.access.user.change-password',
                'description' => 'Change User Passwords',
                'sort' => 6,
            ]),
        ]);

        // Assign Permissions to other Roles
        //
        $newsEditor = Permission::create([
            'type' => User::TYPE_ADMIN,
            'name' => 'admin.access.news',
            'description' => 'All News Permissions',
        ]);

        $newsEditor->children()->saveMany([
            new Permission([
                'type' => User::TYPE_ADMIN,
                'name' => 'admin.access.news.edit',
                'description' => 'Edit News',
            ]),
        ]);

        $eventEditor = Permission::create([
            'type' => User::TYPE_ADMIN,
            'name' => 'admin.access.events',
            'description' => 'All Event Permissions',
        ]);

        $eventEditor->children()->saveMany([
            new Permission([
                'type' => User::TYPE_ADMIN,
                'name' => 'admin.access.events.edit',
                'description' => 'Edit Events',
            ]),
        ]);

        // Assign permissions to the new role (News Editor)
        Role::find(2)->givePermissionTo([
            'admin.access.news.edit',
        ]);

        // Assign permissions to the new role (Event Editor)
        Role::find(3)->givePermissionTo([
            'admin.access.events.edit',
        ]);

        Role::find(1)->givePermissionTo([
            'admin.access.user',
        ]);

        $this->enableForeignKeys();
    }
}
