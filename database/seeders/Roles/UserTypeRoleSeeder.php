<?php

namespace Database\Seeders\Roles;

use App\Domains\Auth\Models\Role;
use App\Domains\Auth\Models\User;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

class UserTypeRoleSeeder extends Seeder
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

    // Create Roles for different user types

    // Student Level Roles
    Role::firstOrCreate([
      'type' => User::TYPE_USER,
      'name' => 'Student',
    ]);

    // Staff Level Roles
    Role::firstOrCreate([
      'type' => User::TYPE_USER,
      'name' => 'Lecturer',
    ]);
    Role::firstOrCreate([
      'type' => User::TYPE_USER,
      'name' => 'Temporary Academic Staff',
    ]);
    Role::firstOrCreate([
      'type' => User::TYPE_USER,
      'name' => 'Academic Support Staff',
    ]);

    $this->enableForeignKeys();
  }
}