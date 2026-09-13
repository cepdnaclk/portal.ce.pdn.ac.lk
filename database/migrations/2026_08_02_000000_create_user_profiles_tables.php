<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    Schema::create('user_profiles', function (Blueprint $table) {
      $table->id();
      $table->string('email')->unique();
      $table->string('alternate_email')->nullable()->index();
      $table->string('full_name')->nullable();
      $table->string('name_with_initials')->nullable();
      $table->string('preferred_short_name')->nullable();
      $table->string('preferred_long_name')->nullable();
      $table->string('honorific', 20)->nullable();
      $table->string('location')->nullable();
      $table->string('current_affiliation')->nullable();
      $table->string('current_position')->nullable();
      $table->string('profile_image')->nullable();
      $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade')->onDelete('set null');
      $table->timestamps();
      $table->softDeletes();
    });

    Schema::create('user_profile_types', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_profile_id')->constrained()->cascadeOnDelete();
      $table->string('type', 20); // STUDENT | ACADEMIC_STAFF | EXTERNAL (varchar, not enum)
      $table->json('attributes')->nullable();
      $table->string('source_key')->nullable()->index();
      $table->timestamps();
      $table->unique(['user_profile_id', 'type']);
      $table->unique(['type', 'source_key']);
    });

    Schema::create('user_profile_links', function (Blueprint $table) {
      $table->id();
      $table->foreignId('user_profile_id')->constrained()->cascadeOnDelete();
      $table->string('type', 30);
      $table->string('url', 500);
      $table->timestamps();
      $table->unique(['user_profile_id', 'type']);
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('user_profile_links');
    Schema::dropIfExists('user_profile_types');
    Schema::dropIfExists('user_profiles');
  }
};