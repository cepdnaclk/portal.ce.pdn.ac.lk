<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_items', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->enum('type', ['info', 'danger', 'warning', 'success'])->default('info');
            $table->string('description');
            $table->string('image')->nullable();
            $table->boolean('enabled')->default(true);
            $table->string('link_url');
            $table->string('link_caption');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_items');
    }
}
