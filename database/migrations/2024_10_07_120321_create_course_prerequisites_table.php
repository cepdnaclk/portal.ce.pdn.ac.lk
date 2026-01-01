<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursePrerequisitesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('course_prerequisites', function (Blueprint $table) {
      $table->id();
      $table->foreignId('course_id')->constrained('courses')->onDelete('cascade');
      $table->foreignId('prerequisite_id')->constrained('courses')->onDelete('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('course_prerequisites');
  }
}