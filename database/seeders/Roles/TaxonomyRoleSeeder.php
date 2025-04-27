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

        $taxonomyManagerRole = Role::updateOrCreate(
            [
                'type' => User::TYPE_USER,
                'name' => 'Taxonomy Manager',
            ],
            []
        );

        $taxonomyPermission = Permission::updateOrCreate(
            [
                'type' => User::TYPE_USER,
                'name' => 'user.taxonomy',
            ],
            [
                'description' => 'Taxonomy Permission',
            ]
        );

        // Define child permissions under 'user.taxonomy'
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
            // Create or update main permission
            $taxonomyType = Permission::updateOrCreate(
                [
                    'type' => User::TYPE_USER,
                    'name' => $permissionData['name'],
                ],
                [
                    'description' => $permissionData['description'] . " Permission",
                ]
            );

            // Link as child of 'user.taxonomy'
            $taxonomyPermission->children()->save($taxonomyType);

            $taxonomyType->children()->saveMany([
                Permission::updateOrCreate(
                    [
                        'type' => User::TYPE_USER,
                        'name' => $permissionData['name'] . '.editor',
                    ],
                    [
                        'description' => $permissionData['description'] . " Editor",
                    ]
                ),
                Permission::updateOrCreate(
                    [
                        'type' => User::TYPE_USER,
                        'name' => $permissionData['name'] . '.viewer',
                    ],
                    [
                        'description' => $permissionData['description'] . " Viewer",
                        'sort' => 2,
                    ]
                ),
            ]);
        }

        // Assign basic taxonomy permission to Administrator role
        Role::findByName('Administrator')->givePermissionTo([
            'user.taxonomy',
        ]);

        // Assign basic taxonomy permission to Taxonomy Manager role
        $taxonomyManagerRole->givePermissionTo([
            'user.taxonomy',
        ]);

        // Only for local and testing environments
        if (app()->environment(['local', 'testing'])) {
            $taxonomyEditorUser = User::updateOrCreate(
                [
                    'email' => env('SEED_USER_EMAIL', 'taxonomy-editor@portal.ce.pdn.ac.lk'),
                ],
                [
                    'type' => User::TYPE_USER,
                    'name' => 'Taxonomy Editor',
                    'password' => bcrypt(env('SEED_USER_PASSWORD', 'password')),
                    'email_verified_at' => now(),
                    'active' => true,
                ]
            );

            $taxonomyEditorUser->givePermissionTo([
                'user.taxonomy',
            ]);
        }

        $this->enableForeignKeys();
    }
}
