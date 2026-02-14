<?php

namespace App\Domains\Tenant\Services;

use App\Domains\Announcement\Models\Announcement;
use App\Domains\ContentManagement\Models\Article;
use App\Domains\ContentManagement\Models\Event;
use App\Domains\ContentManagement\Models\News;
use App\Domains\Tenant\Models\Tenant;
use App\Domains\Auth\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class TenantResolver
{
  public function resolveDefault(): ?Tenant
  {
    return Tenant::default();
  }

  public function resolveBySlug(?string $slug): ?Tenant
  {
    if (! $slug) {
      return null;
    }

    return Tenant::query()->where('slug', $slug)->first();
  }

  public function resolveById($id): ?Tenant
  {
    if (! $id) {
      return null;
    }

    return Tenant::find($id);
  }

  public function resolveFromHost(?string $host): ?Tenant
  {
    if (! $host) {
      return null;
    }

    $normalizedHost = Str::lower($host);

    return Tenant::query()
      ->get()
      ->first(function (Tenant $tenant) use ($normalizedHost) {
        return $tenant->getNormalizedHost() === $normalizedHost;
      });
  }

  public function resolveFromRequest(Request $request): ?Tenant
  {
    $tenantSlug = $request->route('tenant_slug') ?? $request->query('tenant_slug');
    $tenant = $this->resolveBySlug($tenantSlug);

    if ($tenant) {
      return $tenant;
    }

    $tenantId = $request->input('tenant_id');
    $tenant = $this->resolveById($tenantId);

    if ($tenant) {
      return $tenant;
    }

    $modelTenant = $this->resolveFromRouteModel($request);

    if ($modelTenant) {
      return $modelTenant;
    }

    $hostTenant = $this->resolveFromHost($request->getHost());

    return $hostTenant ?? $this->resolveDefault();
  }

  public function resolveFromRouteModel(Request $request): ?Tenant
  {
    $model = $request->route('news')
      ?? $request->route('event')
      ?? $request->route('article')
      ?? $request->route('announcement');

    if ($model instanceof News || $model instanceof Event || $model instanceof Article || $model instanceof Announcement) {
      return $model->tenant;
    }
    $url = $request->route()->uri();

    if (! is_numeric($model)) {
      return null;
    }

    // Find by URL segments
    $lookups = [
      'news' => News::class,
      'events' => Event::class,
      'articles' => Article::class,
      'announcements' => Announcement::class,
    ];

    foreach ($lookups as $segment => $class) {
      if (! Str::contains($url, $segment)) {
        continue;
      }
      return $class::find($model)?->tenant;
    }
    return null;
  }

  public function availableTenantsForUser(?User $user): Collection
  {
    if (! $user) {
      return Tenant::query()->get();
    }

    if ($user->hasAllAccess()) {
      return Tenant::query()->get();
    }

    $userTenants = $user->tenants()->get();
    $roleIds = $user->roles()->pluck('roles.id');

    if ($roleIds->isEmpty()) {
      return $userTenants;
    }

    $roleTenants = Tenant::query()
      ->whereHas('roles', function ($query) use ($roleIds) {
        $query->whereIn('roles.id', $roleIds);
      })
      ->get();

    return $userTenants
      ->merge($roleTenants)
      ->unique('id')
      ->values();
  }
}