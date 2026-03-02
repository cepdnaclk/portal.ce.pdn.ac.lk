<?php

namespace Database\Seeders\Roles;

use App\Domains\Auth\Models\Permission;
use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\User;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class ServicesRoleSeeder extends Seeder
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

    $servicesPermission = Permission::firstOrCreate([
      'type' => User::TYPE_USER,
      'name' => 'user.access.services',
      'description' => 'All Services Permissions',
    ]);

    $emailServicePermission = Permission::firstOrCreate([
      'type' => User::TYPE_USER,
      'name' => 'user.access.services.email',
      'description' => 'Manage Email Service',
    ]);

    $servicesPermission->children()->save($emailServicePermission);

    // Create Roles -------------------------------------------
    $emailServiceRole = Role::firstOrCreate([
      'type' => User::TYPE_USER,
      'name' => 'Email Service Manager',
    ]);


    // Assign Permissions into Roles --------------------------

    // Admins will get all permissions by default
    Role::findByName('Administrator')->givePermissionTo([
      'user.access.services',
    ]);

    // 'Email Service Manager' will get all permissions related to email service
    $emailServiceRole->givePermissionTo([
      'user.access.services.email',
    ]);

    $this->enableForeignKeys();
  }
}
