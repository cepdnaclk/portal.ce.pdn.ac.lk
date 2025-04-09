<?php

namespace Database\Seeders\Roles;

use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\Permission;
use App\Domains\Auth\Models\User;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class TaxonomyRoleSeeder extends Seeder
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
        $taxonomyManagerRole = Role::firstOrCreate([
            'type' => User::TYPE_USER,
            'name' => 'Taxonomy Manager',
        ]);

        $taxonomyManagers = Permission::firstOrCreate([
            'type' => User::TYPE_USER,
            'name' => 'user.taxonomy',
            'description' => 'Taxonomy Permission',
        ]);


        $permissions = [
            [
                'name' => 'user.taxonomy.data',
                'description' => 'Taxonomy Data',
            ],
            [
                'name' => 'user.taxonomy.file',
                'description' => 'Taxonomy File',
            ],
            [
                'name' => 'user.taxonomy.page',
                'description' => 'Taxonomy Page',
            ],
        ];

        foreach ($permissions as $permissionData) {
            $taxonomyType = Permission::firstOrCreate([
                'type' => User::TYPE_USER,
                'name' => $permissionData['name'],
                'description' => $permissionData['description'] . " Permission",
            ]);

            $taxonomyManagers->children()->save($taxonomyType);

            $taxonomyType->children()->saveMany([
                Permission::firstOrCreate(
                    ['name' => $permissionData['name'] . ".editor"],
                    [
                        'type' => User::TYPE_USER,
                        'description' => $permissionData['description'] . " Editor",
                    ]
                ),
                Permission::firstOrCreate(
                    ['name' => $permissionData['name'] . ".viewer"],
                    [
                        'type' => User::TYPE_USER,
                        'description' => $permissionData['description'] . " Viewer",
                        'sort' => 2,
                    ]
                ),
            ]);
        }

        // Admins will get all permissions by default
        Role::findByName('Administrator')->givePermissionTo([
            'user.taxonomy'
        ]);

        // Taxonomy Manager will get all permissions to taxonomy module
        $taxonomyManagerRole->givePermissionTo([
            'user.taxonomy'
        ]);

        $this->enableForeignKeys();
    }
}
