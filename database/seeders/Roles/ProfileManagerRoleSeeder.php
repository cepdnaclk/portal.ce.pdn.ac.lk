<?php

namespace Database\Seeders\Roles;

use App\Domains\Auth\Models\Permission;
use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\User;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class ProfileManagerRoleSeeder extends Seeder
{
  use DisableForeignKeys;

  public function run()
  {
    $this->disableForeignKeys();

    $profilePermission = Permission::firstOrCreate([
      'type' => User::TYPE_USER,
      'name' => 'user.access.profiles',
      'description' => 'All Profile Permissions',
    ]);

    $permissions = [
      'user.access.profiles.view' => 'View Profiles',
      'user.access.profiles.edit' => 'Edit Profiles',
      'user.access.profiles.delete' => 'Delete Profiles',
    ];

    foreach ($permissions as $name => $description) {
      $permission = Permission::firstOrCreate([
        'type' => User::TYPE_USER,
        'name' => $name,
        'description' => $description,
      ]);

      $profilePermission->children()->save($permission);
    }

    $role = Role::firstOrCreate([
      'type' => User::TYPE_USER,
      'name' => 'Profile Manager',
    ]);

    $role->givePermissionTo(['user.access.profiles']);

    Role::findByName('Administrator')->givePermissionTo(['user.access.profiles']);

    if (User::query()->exists()) {
      User::first()->assignRole('Profile Manager');
    }

    $this->enableForeignKeys();
  }
}
