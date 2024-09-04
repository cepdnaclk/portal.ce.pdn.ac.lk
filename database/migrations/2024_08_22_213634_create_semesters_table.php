<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Domains\Semester\Models\Semester;

class CreateSemestersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('semesters', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255);
            $table->integer('version');
            $table->enum('academic_program', array_values(Semester::getAcademicPrograms()));
            $table->text('description')->nullable();
            $table->string('url', 255)->unique();
            $table->timestamps(); // This will create `created_at` and `updated_at` fields automatically
            $table->foreignId('created_by')->constrained('users'); 
            $table->foreignId('updated_by')->nullable()->default(1)->constrained('users'); // Allows null and sets a default value
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('semesters');
    }
}
