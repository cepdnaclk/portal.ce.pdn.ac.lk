<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Tenant\Models\Tenant;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class TenantsTable extends PersistentStateDataTable
{
  public function query(): Builder
  {
    return Tenant::query()->withCount(['users', 'roles', 'news', 'events', 'announcements']);
  }

  public function columns(): array
  {
    return [
      Column::make(__('Name'), 'name')
        ->sortable()
        ->searchable(),
      Column::make(__('Slug'), 'slug')
        ->sortable()
        ->searchable(),
      Column::make(__('URL'), 'url')
        ->sortable(),
      Column::make(__('Default'), 'is_default')
        ->sortable(),
      Column::make(__('Assignments')),
      Column::make(__('Actions')),
    ];
  }

  public function rowView(): string
  {
    return 'backend.tenant.includes.row';
  }
}