<?php

namespace App\Domains\Tenant\Http\Middleware;

use App\Domains\Tenant\Services\TenantResolver;
use Closure;
use Illuminate\Http\Request;

class TenantAccess
{
  public function __construct(private TenantResolver $tenantResolver) {}

  public function handle(Request $request, Closure $next)
  {
    $explicitTenant = $this->tenantResolver->resolveBySlug($request->route('tenant_slug') ?? $request->query('tenant_slug'))
      ?? $this->tenantResolver->resolveById($request->input('tenant_id'))
      ?? $this->tenantResolver->resolveFromRouteModel($request);

    $tenant = $explicitTenant
      ?? $this->tenantResolver->resolveFromHost($request->getHost())
      ?? $this->tenantResolver->resolveDefault();

    if (! $tenant) {
      return $this->handleTenantMissing($request);
    }

    $request->attributes->set('tenant', $tenant);

    $user = $request->user();

    if ($user && $explicitTenant && ! $user->hasAllAccess()) {
      if ($user->isAdmin() && $user->tenants()->count() === 0) {
        return $next($request);
      }

      $hasAccess = $user->tenants()->whereKey($tenant->id)->exists();

      if (! $hasAccess) {
        return $this->handleForbidden($request);
      }
    }

    return $next($request);
  }

  private function handleTenantMissing(Request $request)
  {
    $message = __('Tenant not found.');

    if ($request->expectsJson()) {
      return response()->json(['message' => $message], 404);
    }

    return abort(404, $message);
  }

  private function handleForbidden(Request $request)
  {
    $message = __('You do not have access to that tenant.');

    if ($request->expectsJson()) {
      return response()->json(['message' => $message], 403);
    }

    return redirect()->route('frontend.index')->withFlashDanger($message);
  }
}