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
            'name' => 'CourseManager',
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
        // Create permissions specific to Course Manager
        $courseManager = Permission::create([
            'type' => User::TYPE_ADMIN, 
            'name' => 'admin.access.course',
            'description' => 'All Course Manager Permissions',
        ]);
    
        $courseManager->children()->saveMany([
            new Permission([
                'type' => User::TYPE_ADMIN,
                'name' => 'admin.access.course.create',
                'description' => 'Create Courses',
            ]),
            new Permission([
                'type' => User::TYPE_ADMIN,
                'name' => 'admin.access.course.edit',
                'description' => 'Edit Courses',
            ]),
            new Permission([
                'type' => User::TYPE_ADMIN,
                'name' => 'admin.access.course.delete',
                'description' => 'Delete Courses',
            ]),
            new Permission([
                'type' => User::TYPE_ADMIN,
                'name' => 'admin.access.course.view',
                'description' => 'View Courses',
            ]),
        ]);

        $courseManager2 = Permission::create([
            'type' => User::TYPE_USER, 
            'name' => 'user.access.course',
            'description' => 'All Course Manager Permissions',
        ]);
    
        $courseManager2->children()->saveMany([
            new Permission([
                'type' => User::TYPE_USER,
                'name' => 'user.access.course.create',
                'description' => 'Create Courses',
            ]),
            new Permission([
                'type' => User::TYPE_USER,
                'name' => 'user.access.course.edit',
                'description' => 'Edit Courses',
            ]),
            new Permission([
                'type' => User::TYPE_USER,
                'name' => 'user.access.course.delete',
                'description' => 'Delete Courses',
            ]),
            new Permission([
                'type' => User::TYPE_USER,
                'name' => 'user.access.course.view',
                'description' => 'View Courses',
            ]),
        ]);

         // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // Assign permissions to Roles

        Role::findByName('Administrator')->givePermissionTo([
            'admin.access.user', 'admin.access.course'
        ]);
        Role::findByName('CourseManager')->givePermissionTo(['user.access.course']);

        // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        // Assign Permissions to users 
        if (app()->environment(['local', 'testing'])) {
            User::find(3)->givePermissionTo('user.access.course');
            
            
        };
        

        $this->enableForeignKeys();
    }
}
