<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTaxonomyFilesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('taxonomy_files', function (Blueprint $table) {
      $table->id();
      $table->string('file_name');
      $table->string('file_path')->unique();
      $table->foreignId('taxonomy_id')->nullable()
        ->constrained()->cascadeOnDelete();
      $table->json('metadata');
      $table->foreignId('created_by')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
      $table->foreignId('updated_by')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
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
    Schema::dropIfExists('taxonomy_files');
  }
}