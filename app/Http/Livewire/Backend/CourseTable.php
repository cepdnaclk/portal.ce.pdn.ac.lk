<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Course\Models\Course;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class CourseTable extends DataTableComponent
{
    public array $perPageAccepted = [25, 50, 100];
    public bool $perPageAll = true;

    public string $defaultSortColumn = 'created_at';
    public string $defaultSortDirection = 'desc';

    public function columns(): array
    {
        return [
            Column::make("Code", "code")
                ->searchable(),
            Column::make("Name", "name")
                ->searchable(),
            Column::make("Semester", "semester")
                ->searchable(),
            Column::make("Academic Program", "academic_program")
                ->sortable(),
            Column::make("Type", "type")
                ->sortable(),
            Column::make("Version", "version")
                ->sortable(),
            Column::make("Credits", "credits")
                ->searchable(),
            Column::make("Updated by", "created_by")
                ->sortable(),
            Column::make("Updated At", "updated_at")
                ->sortable(),
            Column::make("Actions")
        ];
    }

    public function query(): Builder
    {
        return Course::query()
            ->when($this->getFilter('semester_id'), fn($query, $type) => $query->where('semester_id', $type));
    }

    public function filters(): array
    {
        $type = ["" => "Any"];
        foreach (Course::getTypes() as $key => $value) {
            $type[$key] = $value;
        }

        return [
            'type' => Filter::make('Type')
                ->select($type),
        ];
    }

    public function rowView(): string
    {
        return 'backend.courses.index-table-row';
    }
}