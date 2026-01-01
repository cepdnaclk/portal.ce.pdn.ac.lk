<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Taxonomy\Models\Taxonomy;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class TaxonomyTable extends DataTableComponent
{
  public array $perPageAccepted = [10, 25, 50, 100];
  public int $perPage = 25;
  public bool $perPageAll = true;

  public string $defaultSortColumn = 'created_at';
  public string $defaultSortDirection = 'desc';

  public function columns(): array
  {
    return [
      Column::make("Code", "code")
        ->searchable()->sortable(),
      Column::make("Name", "name")
        ->searchable()->sortable(),
      Column::make("Created by", "created_by")
        ->sortable(),
      Column::make("Updated by", "updated_by")
        ->sortable(),
      Column::make("Created at", "created_at")
        ->sortable(),
      Column::make("Updated at", "updated_at")
        ->sortable(),
      Column::make("API"),
      Column::make("Actions")
    ];
  }

  public function query(): Builder
  {
    return Taxonomy::query()
      ->when($this->getFilter('visibility'), function ($query, $visible) {
        if ($visible === 1 || $visible === '1') {
          $query->where('visibility', true);
        } elseif ($visible === 2 || $visible === '2') {
          $query->where('visibility', 0);
        }
      })
      ->with('user');
  }

  public function filters(): array
  {
    return [
      'visibility' => Filter::make('Visible to public')
        ->select([
          '' => 'Any',
          1 => 'Visible',
          2 => 'Hidden',
        ]),
    ];
  }

  public function rowView(): string
  {
    return 'backend.taxonomy.index-table-row';
  }
}