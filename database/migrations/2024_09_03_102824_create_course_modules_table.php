<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCourseModulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('course_modules', function (Blueprint $table) {
            $table->id();  // Primary key, auto-incrementing
            $table->foreignId('course_id')->constrained('courses');  // Foreign key to courses.id
            $table->string('topic', 255);  // module topic
            $table->text('description')->nullable();  // Course content, nullable
            $table->integer('order');  // Order of the module within the course
            $table->json('time_allocation')->nullable();  // JSON for time allocation, nullable
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
        Schema::dropIfExists('course_modules');
    }
}
