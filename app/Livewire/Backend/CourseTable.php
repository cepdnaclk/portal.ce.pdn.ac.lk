<?php

namespace App\Livewire\Backend;

use App\Domains\AcademicProgram\Course\Models\Course;
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
                ->searchable()->sortable(),
            Column::make("Name", "name")
                ->searchable()->sortable(),
            Column::make("Semester", "semester")
                ->searchable(),
            Column::make("Academic Program", "academic_program")
                ->sortable(),
            Column::make("Type", "type")
                ->sortable(),
            Column::make("Curriculum", "version")
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
            ->when($this->getFilter('academic_program'), fn($query, $type) => $query->where('academic_program', $type))
            ->when($this->getFilter('semester_id'), fn($query, $type) => $query->where('semester_id', $type))
            ->when($this->getFilter('version'), fn($query, $version) => $query->where('version', $version));;
    }

    public function filters(): array
    {
        $academicProgramOptions = ["" => "Any"];
        foreach (Course::getAcademicPrograms() as $key => $value) {
            $academicProgramOptions[$key] = $value;
        }

        $typeOptions = ["" => "Any"];
        foreach (Course::getTypes() as $key => $value) {
            $typeOptions[$key] = $value;
        }

        $versionOptions = ["" => "Any"];
        foreach (Course::getVersions() as $key => $value) {
            $versionOptions[$key] = $value;
        }

        return [
            // 'academic_program' => Filter::make('Academic Program')
            //     ->select($academicProgramOptions),
            // 'type' => Filter::make('Type')
            //     ->select($typeOptions),
            // 'version' => Filter::make('Curriculum')
            //     ->select($versionOptions),
        ];
    }

    public function rowView(): string
    {
        return 'backend.courses.index-table-row';
    }
}