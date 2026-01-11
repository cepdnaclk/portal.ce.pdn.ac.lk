<?php

use App\Domains\Announcement\Models\Announcement;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddBothToAnnouncementAreaEnum extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    DB::statement("ALTER TABLE announcements MODIFY area ENUM('" . Announcement::TYPE_FRONTEND . "', '" . Announcement::TYPE_BACKEND . "', '" . Announcement::TYPE_BOTH . "') NULL");
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    DB::statement("ALTER TABLE announcements MODIFY area ENUM('" . Announcement::TYPE_FRONTEND . "', '" . Announcement::TYPE_BACKEND . "') NULL");
  }
}
