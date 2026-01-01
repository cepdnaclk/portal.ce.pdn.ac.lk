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

    // Create Permissions -------------------------------------
    $taxonomyManagers = Permission::firstOrCreate([
      'type' => User::TYPE_USER,
      'name' => 'user.access.taxonomy',
      'description' => 'All Taxonomy Permission',
    ]);

    $permissions = [
      [
        'name' => 'user.access.taxonomy.data',
        'description' => 'Manage Taxonomy Data',
      ],
      [
        'name' => 'user.access.taxonomy.file',
        'description' => 'Manage Taxonomy Files',
      ],
      [
        'name' => 'user.access.taxonomy.page',
        'description' => 'Manage Taxonomy Pages',
      ],
      [
        'name' => 'user.access.taxonomy.list',
        'description' => 'Manage Taxonomy Lists',
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
          [
            'type' => User::TYPE_USER,
            'name' => $permissionData['name'] . ".editor",
            'description' => $permissionData['description'] . " Editor",
          ]
        ),
        Permission::firstOrCreate(
          [
            'type' => User::TYPE_USER,
            'name' => $permissionData['name'] . ".viewer",
            'description' => $permissionData['description'] . " Viewer",
          ]
        ),
      ]);
    }


    // Create Roles -------------------------------------------
    $taxonomyManagerRole = Role::firstOrCreate([
      'type' => User::TYPE_USER,
      'name' => 'Taxonomy Manager',
    ]);



    // Assign Permissions into Roles --------------------------

    // Admins will get all permissions by default
    Role::findByName('Administrator')->givePermissionTo([
      'user.access.taxonomy'
    ]);

    // Taxonomy Manager will get all permissions to taxonomy module
    $taxonomyManagerRole->givePermissionTo([
      'user.access.taxonomy'
    ]);


    // Assign Roles into Users --------------------------------
    if (app()->environment(['local', 'testing'])) {
      // Only for the local and testings
      $taxonomyEditorUser = User::firstOrCreate([
        'type' => User::TYPE_USER,
        'name' => 'Taxonomy Manager User',
        'email' => env('SEED_TAXONOMY_MANAGER_EMAIL', 'taxonomy.manager@portal.ce.pdn.ac.lk'),
        'password' => env('SEED_TAXONOMY_MANAGER_PASSWORD', 'taxonomy-manager'),
        'email_verified_at' => now(),
        'active' => true,
      ]);

      $taxonomyEditorUser->assignRole('Taxonomy Manager');
    }

    $this->enableForeignKeys();
  }
}