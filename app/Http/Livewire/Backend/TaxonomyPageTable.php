<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Taxonomy\Models\TaxonomyPage;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Tenant\Services\TenantResolver;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Support\Facades\Cache;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class TaxonomyPageTable extends PersistentStateDataTable
{
  public array $perPageAccepted = [10, 25, 50, 100];
  public int $perPage = 25;
  public bool $perPageAll = true;
  public string $defaultSortColumn = 'created_at';
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
      Column::make('Slug', 'slug')->searchable()->sortable(),
      Column::make('Taxonomy', 'taxonomy.name'),
      Column::make('Tenant', 'tenant.name'),
      Column::make('Created by', 'created_by')->sortable(),
      Column::make('Updated by', 'updated_by')->sortable(),
      Column::make('Created at', 'created_at')->sortable(),
      Column::make('Updated at', 'updated_at')->sortable(),
      Column::make('Actions'),
    ];
  }

  public function query(): Builder
  {
    $tenantIds = $this->getAvailableTenantIds();

    if (! $tenantIds) {
      return TaxonomyPage::query()->whereRaw('1 = 0');
    }

    return TaxonomyPage::query()
      ->with('taxonomy')
      ->with('tenant')
      ->when(! auth()->user()?->hasAllAccess(), function ($query) use ($tenantIds) {
        $query->whereIn('tenant_id', $tenantIds);
      })
      ->when($this->getFilter('tenant'), fn($query, $tenantId) => $query->where('tenant_id', $tenantId))
      ->when($this->getFilter('taxonomy_id'), fn($q, $id) => $q->where('taxonomy_id', $id));
  }

  public function filters(): array
  {
    $tenants = $this->getAvailableTenants();
    $taxonomy = [];
    foreach (Taxonomy::query()->whereIn('tenant_id', $this->getAvailableTenantIds())->get() as $value) {
      $taxonomy[$value->id] = $value->name;
    }

    $filters = [];

    if ($tenants->count() > 1) {
      $filters['tenant'] = Filter::make('Tenant')
        ->select(['' => 'Any'] + $tenants->pluck('name', 'id')->toArray());
    }

    return array_merge($filters, [
      'taxonomy_id' => Filter::make('Taxonomy')->select($taxonomy)
    ]);
  }

  public function rowView(): string
  {
    return 'backend.taxonomy_page.index-table-row';
  }

  private function getAvailableTenants()
  {
    $cacheKey = 'taxonomy_page_table.tenants.user.' . (auth()->id() ?? 'guest');

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
