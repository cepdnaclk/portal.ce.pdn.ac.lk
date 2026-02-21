<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddTenantIdToTaxonomyTables extends Migration
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

    Schema::table('taxonomies', function (Blueprint $table) use ($defaultTenantId) {
      $table->foreignId('tenant_id')
        ->nullable()
        ->default($defaultTenantId)
        ->after('id')
        ->constrained('tenants')
        ->onDelete('restrict');
    });

    Schema::table('taxonomy_files', function (Blueprint $table) use ($defaultTenantId) {
      $table->foreignId('tenant_id')
        ->nullable()
        ->default($defaultTenantId)
        ->after('id')
        ->constrained('tenants')
        ->onDelete('restrict');
    });

    Schema::table('taxonomy_pages', function (Blueprint $table) use ($defaultTenantId) {
      $table->foreignId('tenant_id')
        ->nullable()
        ->default($defaultTenantId)
        ->after('id')
        ->constrained('tenants')
        ->onDelete('restrict');
    });

    Schema::table('taxonomy_lists', function (Blueprint $table) use ($defaultTenantId) {
      $table->foreignId('tenant_id')
        ->nullable()
        ->default($defaultTenantId)
        ->after('id')
        ->constrained('tenants')
        ->onDelete('restrict');
    });

    DB::table('taxonomies')->whereNull('tenant_id')->update(['tenant_id' => $defaultTenantId]);

    if (DB::getDriverName() === 'sqlite') {
      $taxonomyTenantMap = DB::table('taxonomies')
        ->pluck('tenant_id', 'id')
        ->toArray();

      $this->updateTenantIdsForTable('taxonomy_files', $taxonomyTenantMap, $defaultTenantId);
      $this->updateTenantIdsForTable('taxonomy_pages', $taxonomyTenantMap, $defaultTenantId);
      $this->updateTenantIdsForTable('taxonomy_lists', $taxonomyTenantMap, $defaultTenantId);
    } else {
      DB::statement('UPDATE taxonomy_files tf LEFT JOIN taxonomies t ON tf.taxonomy_id = t.id SET tf.tenant_id = COALESCE(t.tenant_id, ?) WHERE tf.tenant_id IS NULL', [$defaultTenantId]);
      DB::statement('UPDATE taxonomy_pages tp LEFT JOIN taxonomies t ON tp.taxonomy_id = t.id SET tp.tenant_id = COALESCE(t.tenant_id, ?) WHERE tp.tenant_id IS NULL', [$defaultTenantId]);
      DB::statement('UPDATE taxonomy_lists tl LEFT JOIN taxonomies t ON tl.taxonomy_id = t.id SET tl.tenant_id = COALESCE(t.tenant_id, ?) WHERE tl.tenant_id IS NULL', [$defaultTenantId]);
    }

    DB::table('taxonomy_files')->whereNull('tenant_id')->update(['tenant_id' => $defaultTenantId]);
    DB::table('taxonomy_pages')->whereNull('tenant_id')->update(['tenant_id' => $defaultTenantId]);
    DB::table('taxonomy_lists')->whereNull('tenant_id')->update(['tenant_id' => $defaultTenantId]);
  }

  public function down()
  {
    Schema::table('taxonomies', function (Blueprint $table) {
      $table->dropForeign(['tenant_id']);
      $table->dropColumn('tenant_id');
    });

    Schema::table('taxonomy_files', function (Blueprint $table) {
      $table->dropForeign(['tenant_id']);
      $table->dropColumn('tenant_id');
    });

    Schema::table('taxonomy_pages', function (Blueprint $table) {
      $table->dropForeign(['tenant_id']);
      $table->dropColumn('tenant_id');
    });

    Schema::table('taxonomy_lists', function (Blueprint $table) {
      $table->dropForeign(['tenant_id']);
      $table->dropColumn('tenant_id');
    });
  }

  private function updateTenantIdsForTable(string $table, array $taxonomyTenantMap, int $defaultTenantId): void
  {
    $rows = DB::table($table)
      ->whereNull('tenant_id')
      ->get(['id', 'taxonomy_id']);

    foreach ($rows as $row) {
      $tenantId = $taxonomyTenantMap[$row->taxonomy_id] ?? $defaultTenantId;
      DB::table($table)
        ->where('id', $row->id)
        ->update(['tenant_id' => $tenantId]);
    }
  }
}
