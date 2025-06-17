<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->boolean('visibility')->default(true)->after('description');
        });
    }

    public function down()
    {
        Schema::table('taxonomies', function (Blueprint $table) {
            $table->dropColumn('visibility');
        });
    }
};
