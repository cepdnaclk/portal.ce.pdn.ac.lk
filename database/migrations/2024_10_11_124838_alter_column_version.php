<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Domains\AcademicProgram\Course\Models\Course;

class AlterColumnVersion extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('semesters', function (Blueprint $table) {
      $table->integer('version')->change();
    });
    Schema::table('courses', function (Blueprint $table) {
      $table->integer('version')->change();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('semesters', function (Blueprint $table) {
      $table->enum('version', array_keys(Course::getVersions()))->change();
    });
    Schema::table('courses', function (Blueprint $table) {
      $table->enum('version', array_keys(Course::getVersions()))->change();
    });
  }
}