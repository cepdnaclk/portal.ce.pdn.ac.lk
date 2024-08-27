<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->text('url');
            $table->date('published_at');
            $table->string('image')->nullable();
            $table->string('link_url')->nullable();
            $table->string('link_caption')->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at')->nullable();
            $table->string('location');
            $table->boolean('enabled')->default(true);
            $table->foreignId('created_by')->constrained('users')->onUpdate('cascade');
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
        Schema::dropIfExists('events');
    }
}