<?php

namespace Database\Seeders;

use App\Domains\Auth\Models\User;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantSeeder extends Seeder
{
  public function run()
  {
    $tenants = config('tenants.tenants', []);
    $defaultSlug = config('tenants.default');

    foreach ($tenants as $tenantData) {
      $slug = $tenantData['slug'] ?? null;
      if (! $slug) {
        continue;
      }

      $tenant = Tenant::updateOrCreate(
        ['slug' => $slug],
        [
          'url' => $tenantData['url'] ?? '',
          'name' => $tenantData['name'] ?? $slug,
          'description' => $tenantData['description'] ?? null,
          'is_default' => $slug === $defaultSlug,
        ]
      );

      // Admins will be assigned all Tenants
      $adminIds = User::query()
        ->where('type', User::TYPE_ADMIN)
        ->whereDoesntHave('tenants')
        ->pluck('id');
      if ($adminIds->isNotEmpty()) {
        $rows = $adminIds->map(fn($userId) => [
          'tenant_id' => $tenant->id,
          'user_id' => $userId,
        ])->all();

        DB::table('tenant_user')->upsert($rows, ['tenant_id', 'user_id']);
      }
    }

    if ($defaultSlug) {
      Tenant::query()->where('slug', '!=', $defaultSlug)->update(['is_default' => false]);
    }
  }
}