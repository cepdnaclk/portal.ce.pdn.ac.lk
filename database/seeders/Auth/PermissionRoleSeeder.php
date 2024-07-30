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
            'type' => User::TYPE_USER,
            'name' => 'Editor',
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

        // Role: Editor
        $users = Permission::create([
            'type' => User::TYPE_USER,
            'name' => 'user.access.editor',
            'description' => 'Editor Permissions',
        ]);
        $users->children()->saveMany([
            new Permission([
                'type' => User::TYPE_USER,
                'name' => 'user.access.editor.news',
                'description' => 'News Articles',
            ]),
            new Permission([
                'type' => User::TYPE_USER,
                'name' => 'user.access.editor.events',
                'description' => 'Event Articles',
            ])
        ]);

        Role::find(1)->givePermissionTo([
            'admin.access.user',
        ]);

        if (app()->environment(['local', 'testing'])) {
            User::find(2)->givePermissionTo('user.access.editor.news');
            User::find(2)->givePermissionTo('user.access.editor.events');
            User::find(3)->givePermissionTo('user.access.editor.news');
            User::find(4)->givePermissionTo('user.access.editor.events');
        };

        $this->enableForeignKeys();
    }
}
