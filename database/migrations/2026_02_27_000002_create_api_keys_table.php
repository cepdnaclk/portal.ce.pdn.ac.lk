<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateApiKeysTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('api_keys', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->uuid('portal_app_id');
      $table->string('key_prefix', 32)->nullable();
      $table->string('key_hash', 128)->unique();
      $table->timestamp('last_used_at')->nullable();
      $table->timestamp('expires_at')->nullable();
      $table->timestamp('revoked_at')->nullable();
      $table->timestamps();

      $table->foreign('portal_app_id')->references('id')->on('portal_apps')->onDelete('cascade');
      $table->index('portal_app_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('api_keys');
  }
}
