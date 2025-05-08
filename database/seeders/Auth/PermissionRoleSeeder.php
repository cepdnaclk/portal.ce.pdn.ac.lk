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


        // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // Create Roles
        Role::create([
            'type' => User::TYPE_ADMIN,
            'name' => 'Administrator',
        ]);

        Role::create([
            'type' => User::TYPE_USER,
            'name' => 'Editor',
        ]);

        Role::create([
            'type' => User::TYPE_USER,
            'name' => 'Course Manager',
        ]);

        // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // Non Grouped Permissions


        // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // Grouped permissions

        // Role: User
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

        // Role: Editor
        $editor = Permission::create([
            'type' => User::TYPE_USER,
            'name' => 'user.access.editor',
            'description' => 'Editor Permissions',
        ]);
        $editor->children()->saveMany([
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

        // Role: CourseManager
        $courseManager = Permission::create([
            'type' => User::TYPE_USER,
            'name' => 'user.access.academic',
            'description' => 'All Course Manager Permissions',
        ]);

        $courseManager->children()->saveMany([
            new Permission([
                'type' => User::TYPE_USER,
                'name' => 'user.access.academic.semesters',
                'description' => 'Semesters',
            ]),
            new Permission([
                'type' => User::TYPE_USER,
                'name' => 'user.access.academic.courses',
                'description' => 'Courses',
            ]),
        ]);

        // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // Assign permissions to Roles

        Role::findByName('Administrator')->givePermissionTo([
            'admin.access.user',
            'user.access.editor',
            'user.access.academic',
        ]);

        Role::findByName('Editor')->givePermissionTo(['user.access.editor']);
        Role::findByName('Course Manager')->givePermissionTo(['user.access.academic']);

        // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // Assign Permissions to users

        // Only for the local testings
        if (app()->environment(['local', 'testing'])) {
            User::find(3)->givePermissionTo('user.access.editor.news');
            User::find(4)->givePermissionTo('user.access.editor.events');
        };

        $this->enableForeignKeys();
    }
}
