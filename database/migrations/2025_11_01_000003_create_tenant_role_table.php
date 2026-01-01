<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTenantRoleTable extends Migration
{
  public function up()
  {
    Schema::create('tenant_role', function (Blueprint $table) {
      $table->id();
      $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
      $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
      $table->timestamps();

      $table->unique(['tenant_id', 'role_id']);
    });
  }

  public function down()
  {
    Schema::dropIfExists('tenant_role');
  }
}