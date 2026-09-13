<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Profile\Models\UserProfile;
use App\Domains\Profile\Models\UserProfileType;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class ProfilesTable extends PersistentStateDataTable
{
  public array $perPageAccepted = [10, 25, 50, 100];
  public int $perPage = 25;

  public string $defaultSortColumn = 'updated_at';
  public string $defaultSortDirection = 'desc';

  public function columns(): array
  {
    return [
      Column::make('Email', 'email')->searchable()->sortable(),
      Column::make('Name with Initials', 'name_with_initials')
        ->searchable(fn(Builder $query, string $term) => $query
          ->orWhere('name_with_initials', 'like', "%{$term}%")
          ->orWhere('full_name', 'like', "%{$term}%"))
        ->sortable(),
      Column::make('Types'),
      Column::make('Account'),
      Column::make('Completeness'),
      Column::make('Updated at', 'updated_at')->sortable(),
      Column::make('Actions'),
    ];
  }

  public function query(): Builder
  {
    return UserProfile::query()
      ->with('profileTypes', 'links', 'user')
      ->when($this->getFilter('type'), function ($query, $type) {
        $query->whereHas('profileTypes', fn($query) => $query->where('type', $type));
      });
  }

  public function filters(): array
  {
    return [
      'type' => Filter::make('Profile Type')
        ->select(['' => 'Any'] + array_combine(UserProfileType::TYPES, UserProfileType::TYPES)),
    ];
  }

  public function rowView(): string
  {
    return 'backend.profiles.index-table-row';
  }
}
