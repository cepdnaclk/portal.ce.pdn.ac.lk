<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Profiles\Models\Profile;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class MyProfilesTable extends PersistentStateDataTable
{
  public array $perPageAccepted = [5, 15];
  public int $perPage = 5;
  public bool $perPageAll = true;

  public string $defaultSortColumn = 'updated_at';
  public string $defaultSortDirection = 'desc';

  public array $filterNames = [
    'type' => 'Profile Type',
  ];

  public function query(): Builder
  {
    return Profile::query()
      ->forUser(auth()->user())
      ->when($this->getFilter('search'), fn($query, $term) => $query->search($term))
      ->when($this->getFilter('type'), fn($query, $type) => $query->where('type', $type));
  }

  public function filters(): array
  {
    return [
      'type' => Filter::make('Profile Type')
        ->select(['' => 'Any'] + Profile::TYPE_LABELS),
    ];
  }

  public function columns(): array
  {
    return [
      Column::make('Type', 'type')->sortable(),
      Column::make('Name', 'preferred_long_name')->sortable(),
      Column::make('E-mail', 'email')->sortable(),
      Column::make('Completeness'),
      Column::make('Updated At', 'updated_at')->sortable(),
      Column::make('Actions'),
    ];
  }

  public function rowView(): string
  {
    return 'profile.my.index-table-row';
  }
}
