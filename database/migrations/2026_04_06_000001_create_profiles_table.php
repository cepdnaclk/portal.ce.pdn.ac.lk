<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('profiles', function (Blueprint $table) {
      $table->id();
      $table->string('email');
      $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
      $table->string('type', 50);
      $table->string('full_name')->nullable();
      $table->string('name_with_initials')->nullable();
      $table->string('preferred_short_name')->nullable();
      $table->string('preferred_long_name')->nullable();
      $table->string('gender', 10)->nullable();
      $table->string('civil_status', 20)->nullable();
      $table->string('honorific', 10)->nullable();
      $table->string('reg_no', 10)->nullable();
      $table->string('profile_picture')->nullable();
      $table->string('current_position')->nullable();
      $table->string('department')->nullable();

      $table->string('phone_number', 50)->nullable();
      $table->string('personal_email')->nullable();
      $table->string('office_email')->nullable();
      $table->text('resident_address')->nullable();

      $table->string('current_location')->nullable();
      $table->json('current_affiliation')->nullable();
      $table->json('previous_affiliations')->nullable();
      $table->longText('biography')->nullable();

      $table->string('profile_url')->nullable();
      $table->string('profile_api')->nullable();

      $table->string('profile_website')->nullable();
      $table->string('profile_cv')->nullable();
      $table->string('profile_linkedin')->nullable();
      $table->string('profile_github')->nullable();
      $table->string('profile_researchgate')->nullable();
      $table->string('profile_google_scholar')->nullable();
      $table->string('profile_orcid')->nullable();
      $table->string('profile_facebook')->nullable();
      $table->string('profile_twitter')->nullable();

      $table->string('review_status', 20)->default('APPROVED');
      $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
      $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
      $table->timestamps();

      $table->unique(['email', 'type']);
      $table->index('email');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('profiles');
  }
}