<?php

namespace App\Support\Concerns;

use App\Domains\Auth\Models\User;
use App\Domains\Tenant\Services\TenantResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

trait ResolvesAvailableTenants
{
  protected function getAvailableTenants(?User $user = null): Collection
  {
    return $this->tenantResolver()
      ->cachedAvailableTenantsForUser($user ?? auth()->user(), $this->tenantSortColumn());
  }

  protected function getAvailableTenantIds($userOrRequest = null): array
  {
    $user = $userOrRequest instanceof Request ? $userOrRequest->user() : $userOrRequest;

    return $this->getAvailableTenants($user)->pluck('id')->all();
  }

  protected function getSelectedTenantId(Collection $tenants): ?int
  {
    $defaultTenantId = $this->tenantResolver()->resolveDefault()?->id;

    if ($defaultTenantId && $tenants->contains('id', $defaultTenantId)) {
      return (int) $defaultTenantId;
    }

    if ($tenants->count() === 1) {
      return (int) $tenants->first()->id;
    }

    return null;
  }

  private function tenantResolver(): TenantResolver
  {
    if (property_exists($this, 'tenantResolver') && $this->tenantResolver instanceof TenantResolver) {
      return $this->tenantResolver;
    }

    return app(TenantResolver::class);
  }

  private function tenantSortColumn(): string
  {
    return property_exists($this, 'tenantSortColumn') ? $this->tenantSortColumn : 'name';
  }
}