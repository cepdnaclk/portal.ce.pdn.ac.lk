<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantUserTable extends Migration
{
  public function up()
  {
    Schema::create('tenant_user', function (Blueprint $table) {
      $table->id();
      $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
      $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
      $table->timestamps();

      $table->unique(['tenant_id', 'user_id']);
    });
  }

  public function down()
  {
    Schema::dropIfExists('tenant_user');
  }
}