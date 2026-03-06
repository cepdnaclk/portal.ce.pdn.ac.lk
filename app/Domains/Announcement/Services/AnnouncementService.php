<?php

namespace App\Domains\Announcement\Services;

use App\Domains\Announcement\Models\Announcement;
use App\Domains\Tenant\Services\TenantResolver;
use App\Services\BaseService;

/**
 * Class AnnouncementService.
 */
class AnnouncementService extends BaseService
{
  public const PORTAL_TENANT_SLUG = 'portal';

  /**
   * AnnouncementService constructor.
   *
   * @param  Announcement  $announcement
   */
  public function __construct(Announcement $announcement, private TenantResolver $tenantResolver)
  {
    $this->model = $announcement;
  }

  /**
   * Get all the enabled announcements
   * For the frontend or globally
   * Where there's either no time frame or
   * if there is a start and end date, make sure the current time is in between that or
   * if there is only a start date, make sure the current time is past that or
   * if there is only an end date, make sure the current time is before that.
   *
   * @param tenantSlug string|null
   * @return mixed
   */
  public function getForFrontend(?string $tenantSlug = null)
  {
    $tenant = $this->resolveTenant($tenantSlug);

    if (! $tenant) {
      return collect();
    }

    return $this->model::enabled()
      ->forArea(Announcement::TYPE_FRONTEND)
      ->forTenant($tenant)
      ->inTimeFrame()
      ->orderBy('starts_at', 'desc')
      ->orderBy('created_at', 'desc')
      ->get();
  }

  /**
   * Get all the enabled announcements
   * For the backend or globally
   * Where there's either no time frame or
   * if there is a start and end date, make sure the current time is in between that or
   * if there is only a start date, make sure the current time is past that or
   * if there is only an end date, make sure the current time is before that.
   *
   * @param  string|null  $tenantSlug
   * @return mixed
   */
  public function getForBackend(?string $tenantSlug = null)
  {
    $tenant = $this->resolveTenant($tenantSlug);

    if (! $tenant) {
      return collect();
    }

    return $this->model::enabled()
      ->forArea(Announcement::TYPE_BACKEND)
      ->forTenant($tenant)
      ->inTimeFrame()
      ->orderBy('starts_at', 'desc')
      ->orderBy('created_at', 'desc')
      ->get();
  }

  /**
   * Resolve tenant by slug or get default tenant
   *
   * @param  string|null  $tenantSlug
   * @return mixed
   */
  private function resolveTenant(?string $tenantSlug)
  {
    if ($tenantSlug !== null) {
      return $this->tenantResolver->resolveBySlug($tenantSlug);
    }

    return $this->tenantResolver->resolveDefault();
  }
}
