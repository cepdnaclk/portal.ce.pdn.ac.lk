<?php

namespace Database\Seeders\Roles;

use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\Permission;
use App\Domains\Auth\Models\User;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class AcademicRoleSeeder extends Seeder
{
    use DisableForeignKeys;
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->disableForeignKeys();

        // Create Permissions -------------------------------------
        $academicManagerPermission = Permission::firstOrCreate([
            'type' => User::TYPE_USER,
            'name' => 'user.access.academic',
            'description' => 'All Academic Permissions',
        ]);

        $permissions = [
            [
                'name' => 'user.access.academic.semesters',
                'description' => 'Manage Semesters',
            ],
            [
                'name' => 'user.access.academic.courses',
                'description' => 'Manage Courses',
            ],
        ];

        foreach ($permissions as $permissionData) {
            $permission = Permission::firstOrCreate([
                'type' => User::TYPE_USER,
                'name' => $permissionData['name'],
                'description' => $permissionData['description'] . " Permission",
            ]);

            $academicManagerPermission->children()->save($permission);
        }


        // Create Roles -------------------------------------------
        $courseManagerRole = Role::firstOrCreate([
            'type' => User::TYPE_USER,
            'name' => 'Course Manager',
        ]);


        // Assign Permissions into Roles --------------------------

        // Admins will get all permissions by default
        Role::findByName('Administrator')->givePermissionTo([
            'user.access.academic'
        ]);

        // Course Manager will get all permissions to course module
        $courseManagerRole->givePermissionTo(['user.access.academic']);


        // Assign Roles into Users --------------------------------
        if (app()->environment(['local', 'testing'])) {
            // Only for the local and testings
            $courseManagerUser = User::firstOrCreate([
                'type' => User::TYPE_USER,
                'name' => 'Course Manager User',
                'email' => env('SEED_COURSE_MANAGER_EMAIL', 'course.manager@portal.ce.pdn.ac.lk'),
                'password' => env('SEED_COURSE_MANAGER_PASSWORD', 'course.manager'),
                'email_verified_at' => now(),
                'active' => true,
            ]);

            $courseManagerUser->assignRole('Course Manager');
        }

        $this->enableForeignKeys();
    }
}
