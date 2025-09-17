<?php

use Illuminate\Database\Migrations\Migration;
use App\Domains\AcademicProgram\Course\Models\Course;

class AlterCourseTypeEnumColumnUpdate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Update the enum field with new types 
        if (Schema::getConnection()->getDriverName() !== 'sqlite') {
            $enumValues = "'" . implode("', '", array_keys(Course::getTypes())) . "'";
            DB::statement("ALTER TABLE courses MODIFY COLUMN type ENUM($enumValues)");
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Nothing here 
    }
}