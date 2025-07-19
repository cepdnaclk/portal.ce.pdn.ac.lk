<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateEnumsToVarchar extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('semesters', function (Blueprint $table) {
            $table->string('academic_program', 16)->change();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->string('type', 16)->change();
            $table->string('academic_program', 16)->change();
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
            $table->enum('academic_program', ['undergraduate', 'postgraduate'])->change();
        });

        Schema::table('courses', function (Blueprint $table) {
            $table->enum('type', ['Foundation', 'Core', 'GE', 'TE'])->change();
            $table->enum('academic_program', ['undergraduate', 'postgraduate'])->change();
        });
    }
}