<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Domains\AcademicProgram\Course\Models\Course;

class CreateCoursesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('courses', function (Blueprint $table) {
      $table->id();  // Primary key, auto-incrementing
      $table->string('code', 16)->unique();  // Course code with a unique constraint
      $table->foreignId('semester_id')->constrained('semesters')->on('semesters')->onDelete('cascade');  // Foreign key to semesters.id
      $table->enum('academic_program', ['undergraduate', 'postgraduate']);  // Enum for academic program
      $table->enum('version', [0, 1, 2]);  // Enum for version as numeric keys
      $table->string('name', 255);  // Course name
      $table->integer('credits')->max(18)->min(0);  // Credit hours
      $table->enum('type', ['Foundation', 'Core', 'GE', 'TE']);  // Enum for course type
      $table->text('content')->nullable();  // Course content, nullable
      $table->json('objectives')->nullable();  // JSON for course objectives, nullable
      $table->json('time_allocation')->nullable();  // JSON for time allocation, nullable
      $table->json('marks_allocation')->nullable();  // JSON for marks allocation, nullable
      $table->json('ilos')->nullable();  // JSON for intended learning outcomes, nullable
      $table->json('references')->nullable();  // JSON for references, nullable
      $table->string('faq_page', 191)->nullable();
      $table->timestamps();
      $table->foreignId('created_by')->constrained('users');  // Foreign key to users.id for created_by
      $table->foreignId('updated_by')->constrained('users');  // Foreign key to users.id for updated_by
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('courses');
  }
}