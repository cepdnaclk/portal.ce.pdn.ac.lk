<?php

namespace App\Domains\ContentManagement\Models\Traits\Scope;

use App\Domains\Tenant\Models\Tenant;

/**
 * Class EventScope.
 */
trait EventScope
{
  /**
   * @param $query
   * @param $tenant
   * @return mixed
   */
  public function scopeForTenant($query, $tenant)
  {
    $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

    return $tenantId ? $query->where('tenant_id', $tenantId) : $query;
  }

  /**
   * @param $query
   * @param array $tenantIds
   * @return mixed
   */
  public function scopeForTenants($query, array $tenantIds)
  {
    return $query->whereIn('tenant_id', $tenantIds);
  }

  /**
   * @param $query
   * @return mixed
   */
  public function scopeEnabled($query)
  {
    return $query->whereEnabled(true);
  }

  /**
   * @param $query
   * @return mixed
   */
  public function scopeGetUpcomingEvents($query)
  {
    return $query->where(function ($query) {
      $query->where(function ($query) {
        $query->where('start_at', '>=', now());
      });
    });
  }

  public function scopeGetPastEvents($query)
  {
    return $query->where(function ($query) {
      $query->where(function ($query) {
        $query->where('start_at', '<=', now());
      });
    });
  }
}
