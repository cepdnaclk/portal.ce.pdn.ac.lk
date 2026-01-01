<?php

namespace Database\Seeders\Auth;

use App\Domains\Auth\Models\User;
use Database\Seeders\Traits\DisableForeignKeys;
use Illuminate\Database\Seeder;

/**
 * Class UserRoleTableSeeder.
 */
class UserRoleSeeder extends Seeder
{
  use DisableForeignKeys;

  /**
   * Run the database seed.
   */
  public function run()
  {
    $this->disableForeignKeys();

    // Admin user
    User::first()->assignRole('Administrator');

    // Only for the local testings
    if (app()->environment(['local', 'testing'])) {
      // Nothing to do here for now
    }

    $this->enableForeignKeys();
  }
}