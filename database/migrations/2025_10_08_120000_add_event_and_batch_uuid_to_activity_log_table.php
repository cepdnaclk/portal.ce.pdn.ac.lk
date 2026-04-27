<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEventAndBatchUuidToActivityLogTable extends Migration
{
  /**
   * Run the migrations.
   */
  public function up()
  {
    $connection = config('activitylog.database_connection');
    $tableName = config('activitylog.table_name');

    Schema::connection($connection)->table($tableName, function (Blueprint $table) use ($connection, $tableName) {
      if (!Schema::connection($connection)->hasColumn($tableName, 'event')) {
        $table->string('event')->nullable()->after('subject_type');
      }

      if (!Schema::connection($connection)->hasColumn($tableName, 'batch_uuid')) {
        $table->uuid('batch_uuid')->nullable()->after('properties');
      }
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down()
  {
    $connection = config('activitylog.database_connection');
    $tableName = config('activitylog.table_name');

    Schema::connection($connection)->table($tableName, function (Blueprint $table) use ($connection, $tableName) {
      if (Schema::connection($connection)->hasColumn($tableName, 'event')) {
        $table->dropColumn('event');
      }

      if (Schema::connection($connection)->hasColumn($tableName, 'batch_uuid')) {
        $table->dropColumn('batch_uuid');
      }
    });
  }
}