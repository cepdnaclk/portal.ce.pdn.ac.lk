<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Semester\Models\Semester;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class SemesterTable extends DataTableComponent
{
    public array $perPageAccepted = [25, 50, 100];
    public bool $perPageAll = true;

    public string $defaultSortColumn = 'created_at';
    public string $defaultSortDirection = 'desc';

    public function columns(): array
    {
        return [
            Column::make("Title", "title")
                ->searchable(),
            Column::make("Version", "version")
                ->sortable(),
            Column::make("Academic Program", "academic_program")
                ->sortable(),
            Column::make("Description", "description")
                ->searchable(),
            // Column::make("URL", "url")
            //     ->searchable(),
            Column::make("Created At", "created_at")
                ->sortable(),
            Column::make("Updated At", "updated_at")
                ->sortable(),
            Column::make("Actions")
        ];
    }

    public function query(): Builder
    {
        return Semester::query()
            ->when($this->getFilter('academic_program'), fn ($query, $type) => $query->where('academic_program', $type));
    }

    public function filters(): array
    {
        $type = ["" => "Any"];
        foreach (Semester::types() as $key => $value) {
            $type[$key] = $value;
        }
        

        return [
            'academic_program' => Filter::make('Academic Program')
                ->select($type),
        ];
    }

    public function rowView(): string
    {
        return 'backend.semesters.index-table-row';
    }
}
