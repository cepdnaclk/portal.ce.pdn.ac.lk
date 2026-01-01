<?php

namespace Database\Seeders;

use App\Domains\Tenant\Models\Tenant;
use Illuminate\Database\Seeder;

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

      Tenant::updateOrCreate(
        ['slug' => $slug],
        [
          'url' => $tenantData['url'] ?? '',
          'name' => $tenantData['name'] ?? $slug,
          'description' => $tenantData['description'] ?? null,
          'is_default' => $slug === $defaultSlug,
        ]
      );

      // TODO - Add Tenant access to all Admin users by default if not exists
    }

    if ($defaultSlug) {
      Tenant::query()->where('slug', '!=', $defaultSlug)->update(['is_default' => false]);
    }
  }
}