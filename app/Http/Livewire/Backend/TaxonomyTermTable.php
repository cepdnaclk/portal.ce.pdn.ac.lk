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

    public $taxonomy;

    public function mount($taxonomy)
    {
        $this->taxonomy = $taxonomy;
    }

    public function columns(): array
    {
        return [
            Column::make("Code", "code")
                ->searchable()->sortable(),
            Column::make("Name", "name")
                ->searchable()->sortable(),
            Column::make("Taxonomy", "taxonomy.name")
                ->searchable()
                ->sortable(),
            Column::make("Created by", "created_by")
                ->sortable(),
            Column::make("Updated by", "updated_by")
                ->sortable(),
            Column::make("Created at", "created_at")
                ->sortable(),
            Column::make("Updated at", "updated_at")
                ->sortable(),
            Column::make("Actions")
        ];
    }

    public function query(): Builder
    {
        return TaxonomyTerm::query()
            ->where('taxonomy_id', $this->taxonomy->id)
            ->with('user');
    }

    public function rowView(): string
    {
        return 'backend.taxonomy.terms.index-table-row'; 
    }
}