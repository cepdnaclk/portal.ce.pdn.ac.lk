<?php

namespace App\Http\Livewire\Backend;

use App\Support\Concerns\ResolvesAvailableTenants;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyList;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class TaxonomyListTable extends PersistentStateDataTable
{
  use ResolvesAvailableTenants;

  protected string $tenantSortColumn = 'slug';

  public array $perPageAccepted = [10, 25, 50, 100];
  public int $perPage = 100;
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
      Column::make('Name', 'name')->searchable()->sortable(),
      Column::make('Taxonomy', 'taxonomy.name')->sortable(),
      Column::make('Tenant', 'tenant.name'),
      Column::make('Data Type', 'data_type')->sortable(),
      Column::make('Items', 'items')->format(fn($value) => is_array($value) ? count($value) : 0),
      Column::make('Created at', 'created_at')->sortable(),
      Column::make('Updated at', 'updated_at')->sortable(),
      Column::make('Actions'),
    ];
  }

  public function query(): Builder
  {
    $tenantIds = $this->getAvailableTenantIds();

    if (! $tenantIds) {
      return TaxonomyList::query()->whereRaw('1 = 0');
    }

    return TaxonomyList::query()
      ->with('taxonomy')
      ->with('tenant')
      ->when(! auth()->user()?->hasAllAccess(), function ($query) use ($tenantIds) {
        $query->whereIn('tenant_id', $tenantIds);
      })
      ->when($this->getFilter('tenant'), fn($query, $tenantId) => $query->where('tenant_id', $tenantId))
      ->when($this->getFilter('taxonomy_id'), fn($query, $taxonomyId) => $query->where('taxonomy_id', $taxonomyId))
      ->when($this->getFilter('data_type'), fn($query, $dataType) => $query->where('data_type', $dataType));
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
      'taxonomy_id' => Filter::make('Taxonomy')->select($taxonomy),
      'data_type' => Filter::make('Data Type')->select(['' => 'All Types'] + TaxonomyList::DATA_TYPE_LABELS),
    ]);
  }

  public function rowView(): string
  {
    return 'backend.taxonomy_list.index-table-row';
  }
}