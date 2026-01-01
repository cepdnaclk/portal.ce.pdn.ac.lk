<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use \Illuminate\Support\Facades\Artisan;

class AddTenantIdToContentTables extends Migration
{
  public function up()
  {
    if (DB::table('tenants')->count() === 0) {
      Artisan::call('db:seed', ['--class' => 'TenantSeeder']);
    }

    $defaultTenantId = DB::table('tenants')
      ->where('slug', config('tenants.default'))
      ->value('id') ?? DB::table('tenants')->value('id');

    if (!$defaultTenantId) {
      throw new RuntimeException('Default tenant not found after seeding. Please ensure at least one tenant exists in the tenants table before running this migration.');
    }

    Schema::table('news', function (Blueprint $table) use ($defaultTenantId) {
      $table->foreignId('tenant_id')
        ->nullable()
        ->default($defaultTenantId)
        ->after('id')
        ->constrained('tenants')
        ->onDelete('restrict');
    });

    Schema::table('events', function (Blueprint $table) use ($defaultTenantId) {
      $table->foreignId('tenant_id')
        ->nullable()
        ->default($defaultTenantId)
        ->after('id')
        ->constrained('tenants')
        ->onDelete('restrict');
    });

    Schema::table('announcements', function (Blueprint $table) use ($defaultTenantId) {
      $table->foreignId('tenant_id')
        ->nullable()
        ->default($defaultTenantId)
        ->after('id')
        ->constrained('tenants')
        ->onDelete('restrict');
    });

    // Set default tenant_id for existing records
    DB::table('news')->whereNull('tenant_id')->update(['tenant_id' => $defaultTenantId]);
    DB::table('events')->whereNull('tenant_id')->update(['tenant_id' => $defaultTenantId]);
    DB::table('announcements')->whereNull('tenant_id')->update(['tenant_id' => $defaultTenantId]);
  }

  public function down()
  {
    Schema::table('news', function (Blueprint $table) {
      $table->dropForeign(['tenant_id']);
      $table->dropColumn('tenant_id');
    });

    Schema::table('events', function (Blueprint $table) {
      $table->dropForeign(['tenant_id']);
      $table->dropColumn('tenant_id');
    });

    Schema::table('announcements', function (Blueprint $table) {
      $table->dropForeign(['tenant_id']);
      $table->dropColumn('tenant_id');
    });
  }
}