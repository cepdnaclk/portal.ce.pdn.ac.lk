<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddAuthorToContentTables extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::table('news', function (Blueprint $table) {
      $table->foreignId('author_id')
        ->nullable()
        ->after('created_by')
        ->constrained('users')
        ->onUpdate('cascade')
        ->onDelete('set null');
    });

    Schema::table('events', function (Blueprint $table) {
      $table->foreignId('author_id')
        ->nullable()
        ->after('created_by')
        ->constrained('users')
        ->onUpdate('cascade')
        ->onDelete('set null');
    });

    Schema::table('articles', function (Blueprint $table) {
      $table->foreignId('author_id')
        ->nullable()
        ->after('created_by')
        ->constrained('users')
        ->onUpdate('cascade')
        ->onDelete('set null');
    });

    DB::table('news')->whereNull('author_id')->update([
      'author_id' => DB::raw('created_by'),
    ]);

    DB::table('events')->whereNull('author_id')->update([
      'author_id' => DB::raw('created_by'),
    ]);

    DB::table('articles')->whereNull('author_id')->update([
      'author_id' => DB::raw('created_by'),
    ]);
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::table('news', function (Blueprint $table) {
      $table->dropConstrainedForeignId('author_id');
    });

    Schema::table('events', function (Blueprint $table) {
      $table->dropConstrainedForeignId('author_id');
    });

    Schema::table('articles', function (Blueprint $table) {
      $table->dropConstrainedForeignId('author_id');
    });
  }
}