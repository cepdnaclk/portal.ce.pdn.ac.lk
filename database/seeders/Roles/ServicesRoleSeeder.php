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

    $emailServiceRole = Role::firstOrCreate([
      'type' => User::TYPE_USER,
      'name' => 'Email Service Manager',
    ]);

    Role::findByName('Administrator')->givePermissionTo([
      'user.access.services',
    ]);

    $emailServiceRole->givePermissionTo([
      'user.access.services.email',
    ]);

    $adminUser = User::first();
    if ($adminUser) {
      $adminUser->assignRole('Email Service Manager');
    }

    $this->enableForeignKeys();
  }
}