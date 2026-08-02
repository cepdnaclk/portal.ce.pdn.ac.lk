<?php

namespace Database\Seeders\Roles;

use App\Domains\Auth\Models\Permission;
use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\User;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class ProfileRoleSeeder extends Seeder
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
    $profileManagers = Permission::firstOrCreate([
      'type' => User::TYPE_USER,
      'name' => 'user.access.profiles',
      'description' => 'All Profiles Permission',
    ]);

    $profileManagers->children()->saveMany([
      Permission::firstOrCreate([
        'type' => User::TYPE_USER,
        'name' => 'user.access.profiles.editor',
        'description' => 'Manage Profiles Editor',
      ]),
      Permission::firstOrCreate([
        'type' => User::TYPE_USER,
        'name' => 'user.access.profiles.viewer',
        'description' => 'Manage Profiles Viewer',
      ]),
    ]);

    // Create Roles -------------------------------------------
    $profileManagerRole = Role::firstOrCreate([
      'type' => User::TYPE_USER,
      'name' => 'Profile Manager',
    ]);

    // Assign Permissions into Roles --------------------------

    // Admins will get all permissions by default
    Role::findByName('Administrator')->givePermissionTo([
      'user.access.profiles',
    ]);

    // Profile Manager will get all permissions to profiles module
    // (children granted explicitly — permission checks do not cascade)
    $profileManagerRole->givePermissionTo([
      'user.access.profiles',
      'user.access.profiles.editor',
      'user.access.profiles.viewer',
    ]);

    // Assign Roles into Users --------------------------------
    if (app()->environment(['local', 'testing'])) {
      // Only for the local and testings
      $profileManagerUser = User::firstOrCreate([
        'type' => User::TYPE_USER,
        'name' => 'Profile Manager User',
        'email' => env('SEED_PROFILE_MANAGER_EMAIL', 'profile.manager@portal.ce.pdn.ac.lk'),
        'password' => env('SEED_PROFILE_MANAGER_PASSWORD', 'profile-manager'),
        'email_verified_at' => now(),
        'active' => true,
      ]);

      $profileManagerUser->assignRole('Profile Manager');
    }

    $this->enableForeignKeys();
  }
}