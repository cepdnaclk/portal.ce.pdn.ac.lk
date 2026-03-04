<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmailDeliveryLogsTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('email_delivery_logs', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->uuid('portal_app_id');
      $table->uuid('api_key_id');
      $table->string('from');
      $table->json('to');
      $table->json('cc')->nullable();
      $table->json('bcc')->nullable();
      $table->string('subject');
      $table->string('template')->nullable();
      $table->json('metadata')->nullable();
      $table->string('provider_message_id')->nullable();
      $table->enum('status', ['queued', 'sent', 'failed'])->default('queued');
      $table->text('failure_reason')->nullable();
      $table->timestamp('sent_at')->nullable();
      $table->timestamps();

      $table->foreign('portal_app_id')->references('id')->on('portal_apps')->onDelete('cascade');
      $table->foreign('api_key_id')->references('id')->on('api_keys')->onDelete('cascade');
      $table->index(['portal_app_id', 'status']);
      $table->index('api_key_id');
      $table->index('created_at');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('email_delivery_logs');
  }
}
