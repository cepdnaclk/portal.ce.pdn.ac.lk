<?php

namespace App\Http\Controllers;

use App\Domains\Auth\Models\User;
use App\Domains\Tenant\Services\TenantResolver;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;

/**
 * Class Controller.
 */
class Controller extends BaseController
{
  use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

  /**
   * Build author dropdown options.
   */
  protected function getAuthorOptions()
  {
    return User::query()
      ->orderBy('name')
      ->get()
      ->mapWithKeys(function ($user) {
        $label = $user->name ?: $user->email ?: ('User #' . $user->id);
        if ($user->name && $user->email) {
          $label = $user->name . ' (' . $user->email . ')';
        }
        return [$user->id => $label];
      });
  }

  /**
   * Resolve tenant id, defaulting when a user has only one available tenant.
   *
   * Supports both call patterns:
   * - resolveTenantId($request, $tenantResolver)
   * - resolveTenantId($request, $availableTenantIds)
   *
   * @param TenantResolver|array<int, int|string> $tenantResolverOrAllowedIds
   */
  protected function resolveTenantId(Request $request, $tenantResolverOrAllowedIds): ?int
  {
    if ($request->filled('tenant_id')) {
      return (int) $request->input('tenant_id');
    }

    if ($tenantResolverOrAllowedIds instanceof TenantResolver) {
      $tenants = $tenantResolverOrAllowedIds->availableTenantsForUser($request->user());
      $defaultTenantId = $tenantResolverOrAllowedIds->resolveDefault()?->id;

      if ($defaultTenantId && $tenants->contains('id', $defaultTenantId)) {
        return (int) $defaultTenantId;
      }

      if ($tenants->count() === 1) {
        return (int) $tenants->first()->id;
      }

      return null;
    }

    if (is_array($tenantResolverOrAllowedIds) && count($tenantResolverOrAllowedIds) === 1) {
      return (int) array_values($tenantResolverOrAllowedIds)[0];
    }

    return null;
  }
}