<?php

namespace Database\Seeders\Roles;

use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\Permission;
use App\Domains\Auth\Models\User;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class EditorRoleSeeder extends Seeder
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
    $editorPermission = Permission::firstOrCreate([
      'type' => User::TYPE_USER,
      'name' => 'user.access.editor',
      'description' => 'All Editor Permissions',
    ]);

    $permissions = [
      [
        'name' => 'user.access.editor.articles',
        'description' => 'Manage Articles',
      ],
      [
        'name' => 'user.access.editor.news',
        'description' => 'Manage News Posts',
      ],
      [
        'name' => 'user.access.editor.events',
        'description' => 'Manage Event Articles',
      ],
      [
        'name' => 'user.access.editor.announcements',
        'description' => 'Manage Announcements',
      ],
    ];

    foreach ($permissions as $permissionData) {
      $permission = Permission::firstOrCreate([
        'type' => User::TYPE_USER,
        'name' => $permissionData['name'],
        'description' => $permissionData['description'] . " Permission",
      ]);

      $editorPermission->children()->save($permission);
    }


    // Create Roles -------------------------------------------
    $editorRole = Role::firstOrCreate([
      'type' => User::TYPE_USER,
      'name' => 'Editor',
    ]);


    // Assign Permissions into Roles --------------------------

    // Admins will get all permissions by default
    Role::findByName('Administrator')->givePermissionTo([
      'user.access.editor',
    ]);

    // Editor will get all permissions to editing related module
    $editorRole->givePermissionTo(['user.access.editor']);


    // Assign Roles into Users --------------------------------
    if (app()->environment(['local', 'testing'])) {
      // Only for the local and testings
      $editorUser = User::firstOrCreate([
        'type' => User::TYPE_USER,
        'name' => 'Editor User',
        'email' => env('SEED_EVENT_EDITOR_EMAIL', 'editor@portal.ce.pdn.ac.lk'),
        'password' => env('SEED_EVENT_EDITOR_PASSWORD', 'editor'),
        'email_verified_at' => now(),
        'active' => true,
      ]);


      $editorUser->assignRole('Editor');
    }

    $this->enableForeignKeys();
  }
}