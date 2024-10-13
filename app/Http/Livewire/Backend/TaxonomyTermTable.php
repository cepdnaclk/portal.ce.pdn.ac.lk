<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Taxonomy\Models\TaxonomyTerm;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;

class TaxonomyTermTable extends DataTableComponent
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
            Column::make("Actions")
        ];
    }

    public function query(): Builder
    {
        return TaxonomyTerm::query(); 
    }

    public function rowView(): string
    {
        return 'backend.taxonomy.terms.index-table-row'; 
    }
}
