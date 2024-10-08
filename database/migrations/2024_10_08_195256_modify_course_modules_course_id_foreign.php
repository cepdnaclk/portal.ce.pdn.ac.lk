<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyCourseModulesCourseIdForeign extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('course_modules', function (Blueprint $table) {
            // Drop the current foreign key
            $table->dropForeign(['course_id']);
            
            // Recreate the foreign key with 'onDelete' cascade
            $table->foreign('course_id')
                ->references('id')
                ->on('courses')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('course_modules', function (Blueprint $table) {
            // Drop the new foreign key
            $table->dropForeign(['course_id']);
            
            // Restore the old foreign key without 'onDelete' cascade
            $table->foreign('course_id')
                ->references('id')
                ->on('courses');
        });
    }
}

