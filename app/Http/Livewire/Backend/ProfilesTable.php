<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Profiles\Models\Profile;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class ProfilesTable extends PersistentStateDataTable
{
  public array $perPageAccepted = [10, 25, 50, 100, 250];
  public int $perPage = 25;
  public bool $perPageAll = true;

  public string $defaultSortColumn = 'updated_at';
  public string $defaultSortDirection = 'desc';

  public array $filterNames = [
    'type' => 'Profile Type',
    'linked' => 'Linked User',
  ];

  public function query(): Builder
  {
    abort_unless(auth()->user()?->can('user.access.profiles.view') || auth()->user()?->hasAllAccess(), 403);

    return Profile::query()
      ->with(['user'])
      ->when($this->getFilter('search'), fn($query, $term) => $query->search($term))
      ->when($this->getFilter('type'), fn($query, $type) => $query->where('type', $type))
      ->when($this->getFilter('linked'), function ($query, $linked) {
        if ($linked === 'yes') {
          $query->whereNotNull('user_id');
        } elseif ($linked === 'no') {
          $query->whereNull('user_id');
        }
      });
  }

  public function filters(): array
  {
    return [
      'type' => Filter::make('Profile Type')
        ->select(['' => 'Any'] + Profile::TYPE_LABELS),
      'linked' => Filter::make('Linked User')
        ->select([
          '' => 'Any',
          'yes' => 'Linked',
          'no' => 'Independent',
        ]),
    ];
  }

  public function columns(): array
  {
    return [
      Column::make('Type', 'type')->sortable(),
      Column::make('Name', 'full_name')->sortable(),
      Column::make('E-mail', 'email')->sortable(),
      Column::make('Linked User', 'user.name')->sortable(),
      Column::make('Completeness'),
      Column::make('Updated At', 'updated_at')->sortable(),
      Column::make('Actions'),
    ];
  }

  public function rowView(): string
  {
    return 'profile.admin.index-table-row';
  }
}
