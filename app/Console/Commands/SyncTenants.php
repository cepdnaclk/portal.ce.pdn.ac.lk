<?php

namespace App\Console\Commands;

use App\Domains\Tenant\Models\Tenant;
use Illuminate\Console\Command;

class SyncTenants extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'tenants:sync';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Sync tenants from config/tenants.php';

  public function handle(): int
  {
    $tenants = config('tenants.tenants', []);
    $defaultSlug = config('tenants.default');

    $synced = 0;

    foreach ($tenants as $tenantData) {
      $slug = $tenantData['slug'] ?? null;
      if (! $slug) {
        continue;
      }

      Tenant::updateOrCreate(
        ['slug' => $slug],
        [
          'name' => $tenantData['name'] ?? $slug,
          'url' => $tenantData['url'] ?? '',
          'description' => $tenantData['description'] ?? null,
          'is_default' => $slug === $defaultSlug,
        ]
      );

      $synced++;
    }

    if ($defaultSlug) {
      Tenant::query()->where('slug', '!=', $defaultSlug)->update(['is_default' => false]);
    }

    $this->info("Synced {$synced} tenants.");

    return self::SUCCESS;
  }
}