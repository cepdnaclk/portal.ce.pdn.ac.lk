<?php

namespace App\Http\Livewire\Backend;

use App\Support\Concerns\ResolvesAvailableTenants;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyFile as ModelsTaxonomyFile;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class TaxonomyFileTable extends PersistentStateDataTable
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
      Column::make('File Name (Slug)', 'file_name')->searchable()->sortable(),
      Column::make('Taxonomy', 'taxonomy.name'),
      Column::make('Tenant', 'tenant.name'),
      Column::make("Created by", "created_by")->sortable(),
      Column::make("Updated by", "updated_by")->sortable(),
      Column::make('Created at', 'created_at')->sortable(),
      Column::make('Updated at', 'updated_at')->sortable(),
      Column::make('Actions'),
    ];
  }

  public function query(): Builder
  {
    $tenantIds = $this->getAvailableTenantIds();

    if (! $tenantIds) {
      return ModelsTaxonomyFile::query()->whereRaw('1 = 0');
    }

    return ModelsTaxonomyFile::query()
      ->with('taxonomy')
      ->with('tenant')
      ->when(! auth()->user()?->hasAllAccess(), function ($query) use ($tenantIds) {
        $query->whereIn('tenant_id', $tenantIds);
      })
      ->when($this->getFilter('tenant'), fn($query, $tenantId) => $query->where('tenant_id', $tenantId))
      ->when($this->getFilter('taxonomy_id'), fn($query, $type) => $query->where('taxonomy_id', $type));
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
    return 'backend.taxonomy_file.index-table-row';
  }
}