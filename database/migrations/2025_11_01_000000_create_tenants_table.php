<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantsTable extends Migration
{
  public function up()
  {
    Schema::create('tenants', function (Blueprint $table) {
      $table->id();
      $table->string('slug')->unique();
      $table->string('name');
      $table->string('url');
      $table->string('description')->nullable();
      $table->boolean('is_default')->default(false);
      $table->timestamps();
    });
  }

  public function down()
  {
    Schema::dropIfExists('tenants');
  }
}