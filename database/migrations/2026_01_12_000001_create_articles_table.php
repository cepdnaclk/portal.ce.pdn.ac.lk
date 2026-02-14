<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticlesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('articles', function (Blueprint $table) {
      $table->id();
      $table->foreignId('tenant_id')
        ->constrained('tenants')
        ->onDelete('restrict');
      $table->string('title');
      $table->text('content');
      $table->timestampTz('published_at')->useCurrent();
      $table->jsonb('categories_json')->nullable();
      $table->jsonb('gallery_json')->nullable();
      $table->jsonb('content_images_json')->nullable();
      $table->foreignId('created_by')->constrained('users')->onUpdate('cascade');
      $table->foreignId('updated_by')->nullable()->constrained('users')->onUpdate('cascade');
      $table->timestamps();

      $table->index('tenant_id');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('articles');
  }
}