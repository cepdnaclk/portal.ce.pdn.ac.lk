<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Taxonomy\Models\Taxonomy;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class TaxonomyTable extends DataTableComponent
{
    public array $perPageAccepted = [10, 25, 50];
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
            Column::make("Actions")
        ];
    }

    public function query(): Builder
    {
        return Taxonomy::query();
    }

    public function rowView(): string
    {
        return 'backend.taxonomies.index-table-row';
    }
}

