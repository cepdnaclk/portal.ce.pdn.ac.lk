<?php

namespace App\Http\Livewire\Backend;

use App\Domains\AcademicProgram\Semester\Models\Semester;
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
                ->searchable()->sortable(),
            Column::make("Curriculum", "version")
                ->sortable(),
            Column::make("Academic Program", "academic_program")
                ->sortable(),
            Column::make("Description", "description")
                ->searchable(),
            Column::make("URL", "url")
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
        return Semester::query()
            ->when($this->getFilter('academic_program'), fn($query, $type) => $query->where('academic_program', $type))
            ->when($this->getFilter('version'), fn($query, $version) => $query->where('version', $version));
    }

    public function filters(): array
    {
        $academicProgramOptions = ["" => "Any"];
        foreach (Semester::getAcademicPrograms() as $key => $value) {
            $academicProgramOptions[$key] = $value;
        }
        $versionOptions = ["" => "Any"];
        foreach (Semester::getVersions() as $key => $value) {
            $versionOptions[$key] = $value;
        }


        return [
            'academic_program' => Filter::make('Academic Program')
                ->select($academicProgramOptions),
            'version' => Filter::make('Version')
                ->select($versionOptions),
        ];
    }

    public function rowView(): string
    {
        return 'backend.semesters.index-table-row';
    }
}