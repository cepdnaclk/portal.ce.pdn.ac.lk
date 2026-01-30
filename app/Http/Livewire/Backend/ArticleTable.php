<?php

namespace App\Http\Livewire\Backend;

use App\Domains\ContentManagement\Models\Article;
use App\Domains\Tenant\Services\TenantResolver;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Cache;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class ArticleTable extends PersistentStateDataTable
{
  public array $perPageAccepted = [25, 50, 100];
  public bool $perPageAll = true;
  public int $perPage = 25;

  public string $defaultSortColumn = 'published_at';
  public string $defaultSortDirection = 'desc';

  public function mount(): void
  {
    $tenantIds = $this->getAvailableTenantIds();
    if (count($tenantIds) === 1 && empty($this->filters['tenant'])) {
      $this->filters['tenant'] = $tenantIds[0];
    }
  }

  public function columns(): array
  {
    return [
      Column::make('Title', 'title')
        ->searchable(),
      Column::make('Categories'),
      Column::make('Content'),
      Column::make('Tenant', 'tenant.name'),
      Column::make('Author', 'user.name')
        ->sortable()
        ->searchable(),
      Column::make('Published at', 'published_at')
        ->sortable(),
      Column::make('Actions'),
    ];
  }

  public function query(): Builder
  {
    $tenantIds = $this->getAvailableTenantIds();

    if (! $tenantIds) {
      return Article::query()->whereRaw('1 = 0');
    }

    return Article::query()
      ->with(['tenant', 'user'])
      ->when(! auth()->user()->hasAllAccess(), function ($query) use ($tenantIds) {
        $query->whereIn('tenant_id', $tenantIds);
      })
      ->when($this->getFilter('tenant'), function ($query, $tenantId) {
        $query->where('tenant_id', $tenantId);
      })
      ->orderBy('published_at', 'desc');
  }

  public function filters(): array
  {
    $tenants = $this->getAvailableTenants();
    $filters = [];

    if ($tenants->count() > 1) {
      $filters['tenant'] = Filter::make('Tenant')
        ->select(['' => 'Any'] + $tenants->pluck('name', 'id')->toArray());
    }

    return $filters;
  }

  public function rowView(): string
  {
    return 'backend.article.index-table-row';
  }

  private function getAvailableTenants()
  {
    $cacheKey = 'article_table.tenants.user.' . (auth()->id() ?? 'guest');

    return Cache::remember($cacheKey, 60, function () {
      return app(TenantResolver::class)
        ->availableTenantsForUser(auth()->user())
        ->sortBy('slug')
        ->values();
    });
  }

  private function getAvailableTenantIds(): array
  {
    return $this->getAvailableTenants()->pluck('id')->all();
  }
}