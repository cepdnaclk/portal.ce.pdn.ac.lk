<?php

namespace App\Domains\Tenant\Services;

use App\Domains\Announcement\Models\Announcement;
use App\Domains\ContentManagement\Models\Article;
use App\Domains\ContentManagement\Models\Event;
use App\Domains\ContentManagement\Models\News;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyFile;
use App\Domains\Taxonomy\Models\TaxonomyList;
use App\Domains\Taxonomy\Models\TaxonomyPage;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
use App\Domains\Tenant\Models\Tenant;
use App\Domains\Auth\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class TenantResolver
{
  private const AVAILABLE_TENANTS_CACHE_MINUTES = 10;

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
      ?? $request->route('announcement')
      ?? $request->route('article')
      ?? $request->route('taxonomy')
      ?? $request->route('taxonomyList')
      ?? $request->route('taxonomyFile')
      ?? $request->route('taxonomyPage')
      ?? $request->route('term');

    if (
      $model instanceof News
      || $model instanceof Event
      || $model instanceof Announcement
      || $model instanceof Article
      || $model instanceof Taxonomy
      || $model instanceof TaxonomyFile
      || $model instanceof TaxonomyPage
      || $model instanceof TaxonomyList
    ) {
      return $model->tenant;
    }

    if ($model instanceof TaxonomyTerm) {
      return $model->taxonomy?->tenant;
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
      'taxonomy' => Taxonomy::class,
      'taxonomy-files' => TaxonomyFile::class,
      'taxonomy-pages' => TaxonomyPage::class,
      'taxonomy-lists' => TaxonomyList::class,
      'terms' => TaxonomyTerm::class,
    ];

    foreach ($lookups as $segment => $class) {
      if (! Str::contains($url, $segment)) {
        continue;
      }
      if ($class === TaxonomyTerm::class) {
        return $class::find($model)?->taxonomy?->tenant;
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

  public function cachedAvailableTenantsForUser(?User $user, string $sortBy = 'name'): Collection
  {
    $cacheKey = $this->availableTenantsCacheKey($user, $sortBy);

    return Cache::remember($cacheKey, now()->addMinutes(self::AVAILABLE_TENANTS_CACHE_MINUTES), function () use ($user, $sortBy) {
      return $this->availableTenantsForUser($user)
        ->sortBy($sortBy)
        ->values();
    });
  }

  public function forgetCachedAvailableTenantsForUser(?User $user): void
  {
    foreach (['name', 'slug'] as $sortBy) {
      Cache::forget($this->availableTenantsCacheKey($user, $sortBy));
    }
  }

  private function availableTenantsCacheKey(?User $user, string $sortBy): string
  {
    return sprintf(
      'tenant_resolver.available_tenants.user.%s.sort.%s',
      $user?->getAuthIdentifier() ?? 'guest',
      $sortBy
    );
  }
}