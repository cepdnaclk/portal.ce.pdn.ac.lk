<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseTable extends Migration
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
            $table->foreignId('semester_id')->constrained('semesters');  // Foreign key to semesters.id
            $table->enum('academic_program', ['Undergraduate', 'Postgraduate']);  // Enum for academic program
            $table->integer('version');  // Version for curriculum
            $table->string('name', 255);  // Course name
            $table->integer('credits');  // Credit hours
            $table->enum('type', ['Core', 'General Elective', 'Technical Elective']);  // Enum for course type
            $table->text('content')->nullable();  // Course content, nullable
            $table->json('objectives')->nullable();  // JSON for course objectives, nullable
            $table->json('time_allocation')->nullable();  // JSON for time allocation, nullable
            $table->json('marks_allocation')->nullable();  // JSON for marks allocation, nullable
            $table->json('ilos')->nullable();  // JSON for intended learning outcomes, nullable
            $table->json('urls')->nullable();  // JSON for URLs, nullable
            $table->json('references')->nullable();  // JSON for references, nullable
            $table->timestamps();  // Created_at and updated_at
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
        Schema::dropIfExists('course');
    }
}
