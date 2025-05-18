<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyFile as ModelsTaxonomyFile;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class TaxonomyFileTable extends DataTableComponent
{
    public array $perPageAccepted = [10, 25, 50];

    public bool $perPageAll = true;

    public string $defaultSortColumn = 'created_at';
    public string $defaultSortDirection = 'desc';

    public function columns(): array
    {
        return [
            Column::make('File Name (Slug)', 'file_name')
                ->searchable()
                ->sortable(),

            Column::make('Taxonomy', 'taxonomy.name'),
            Column::make("Created by", "created_by")
                ->sortable(),
            Column::make("Updated by", "updated_by")
                ->sortable(),
            Column::make('Created at', 'created_at')
                ->sortable(),
            Column::make('Updated at', 'updated_at')
                ->sortable(),
            Column::make('Actions'),
        ];
    }

    public function query(): Builder
    {
        return ModelsTaxonomyFile::query()
            ->when($this->getFilter('taxonomy_id'), fn($query, $type) => $query->where('taxonomy_id', $type));
    }


    public function filters(): array
    {
        $taxonomy = [];

        foreach (Taxonomy::query()->get() as $value) {
            $taxonomy[$value->id] = $value->name;
        }

        return [
            'taxonomy_id' => Filter::make('Taxonomy')
                ->select($taxonomy)
        ];
    }

    public function rowView(): string
    {
        return 'backend.taxonomy_file.index-table-row';
    }
}