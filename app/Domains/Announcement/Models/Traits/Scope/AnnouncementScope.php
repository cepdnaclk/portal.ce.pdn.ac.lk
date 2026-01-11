<?php

namespace App\Domains\Announcement\Models\Traits\Scope;

use App\Domains\Announcement\Models\Announcement;
use App\Domains\Tenant\Models\Tenant;

/**
 * Class AnnouncementScope.
 */
trait AnnouncementScope
{
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
   * @param $tenant
   * @return mixed
   */
  public function scopeForTenant($query, $tenant = null)
  {
    if ($tenant instanceof Tenant) {
      $tenantId = $tenant->id;
    } elseif (is_int($tenant) || is_string($tenant)) {
      $tenantId = $tenant;
    } else {
      $defaultTenant = Tenant::default();
      dd($defaultTenant);
      if ($defaultTenant === null) {
        throw new \RuntimeException('No default tenant configured.');
      }
      $tenantId = $defaultTenant->id;
    }
    return $query->where('tenant_id', $tenantId);
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
   * @param $area
   * @return mixed
   */
  public function scopeForArea($query, $area)
  {
    $areas = match ($area) {
      Announcement::TYPE_FRONTEND => [Announcement::TYPE_FRONTEND, Announcement::TYPE_BOTH],
      Announcement::TYPE_BACKEND => [Announcement::TYPE_BACKEND, Announcement::TYPE_BOTH],
      Announcement::TYPE_BOTH => [Announcement::TYPE_BOTH],
      default => [$area],
    };

    return $query->where(function ($query) use ($areas) {
      $query->whereIn('area', $areas)
        ->orWhereNull('area');
    });
  }

  /**
   * @param $query
   * @return mixed
   */
  public function scopeInTimeFrame($query)
  {
    return $query->where(function ($query) {
      $query->where(function ($query) {
        $query->whereNull('starts_at')
          ->whereNull('ends_at');
      })->orWhere(function ($query) {
        $query->whereNotNull('starts_at')
          ->whereNotNull('ends_at')
          ->where('starts_at', '<=', now())
          ->where('ends_at', '>=', now());
      })->orWhere(function ($query) {
        $query->whereNotNull('starts_at')
          ->whereNull('ends_at')
          ->where('starts_at', '<=', now());
      })->orWhere(function ($query) {
        $query->whereNull('starts_at')
          ->whereNotNull('ends_at')
          ->where('ends_at', '>=', now());
      });
    });
  }
}
