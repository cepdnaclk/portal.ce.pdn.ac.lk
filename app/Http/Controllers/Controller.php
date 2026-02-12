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
   * Resolve tenant id, defaulting when a user only has one tenant.
   */
  protected function resolveTenantId(Request $request, TenantResolver $tenantResolver): ?int
  {
    if ($request->filled('tenant_id')) {
      return (int) $request->input('tenant_id');
    }

    $tenants = $tenantResolver->availableTenantsForUser($request->user());

    if ($tenants->count() === 1) {
      return (int) $tenants->first()->id;
    }

    return null;
  }
}
