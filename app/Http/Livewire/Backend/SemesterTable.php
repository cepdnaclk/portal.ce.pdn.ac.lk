<?php

namespace App\Http\Livewire\Backend;

use App\Domains\AcademicProgram\Semester\Models\Semester;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\{ButtonGroupColumn, LinkColumn};
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class SemesterTable extends PersistentStateDataTable
{
  protected $model = Semester::class;

  public function configure(): void
  {
    parent::configure();

    $this->setDefaultSort('created_at', 'desc');
    $this->setPerPage(25);
    $this->setPerPageAccepted([25, 50, 100, -1]);
  }

  public function columns(): array
  {
    return [
      Column::make("Title", "title")
        ->searchable()
        ->sortable(),
      Column::make("Curriculum", "version")
        ->format(fn($value, Semester $semester) => Semester::getVersions()[$semester->version] ?? 'Unknown Version')
        ->sortable(),
      Column::make("Academic Program", "academic_program")
        ->format(fn($value, Semester $semester) => $semester->academicProgram())
        ->sortable(),
      Column::make("Description", "description")
        ->searchable(),
      Column::make("URL", "url")
        ->sortable()
        ->searchable()
        ->format(fn($value, Semester $semester) => sprintf(
          '<a href="https://www.ce.pdn.ac.lk/academics/%s/semesters/%s" target="_blank">/%s</a>',
          strtolower($semester->academic_program),
          $semester->url,
          $semester->url
        ))->html(),
      Column::make("Updated by", "updated_by")
        ->format(fn($value, Semester $semester) => $semester->updatedUser->name ?? '')
        ->sortable(),
      Column::make("Updated At", "updated_at")
        ->sortable(),
      ButtonGroupColumn::make('Actions')
        ->attributes(fn() => [
          'class' => 'btn-group',
          'role' => 'group',
        ])
        ->buttons([
          LinkColumn::make('Edit')
            ->title(fn() => '<i class="fa fa-pencil"></i>')
            ->location(fn($row) => route('dashboard.semesters.edit', $row))
            ->attributes(fn() => [
              'class' => 'btn btn-info btn-xs',
            ])->html(),
          LinkColumn::make('Delete')
            ->title(fn() => '<i class="fa fa-trash"></i>')
            ->location(fn($row) => route('dashboard.semesters.delete', $row))
            ->attributes(fn() => [
              'class' => 'btn btn-danger btn-xs',
            ])->html(),
        ])
        ->excludeFromColumnSelect(),
    ];
  }

  public function builder(): Builder
  {
    return Semester::query()
      ->when($this->getAppliedFilterWithValue('academic_program'), fn($query, $type) => $query->where('academic_program', $type))
      ->when($this->getAppliedFilterWithValue('version'), fn($query, $version) => $query->where('version', $version));
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
      SelectFilter::make('Academic Program', 'academic_program')
        ->options($academicProgramOptions),
      SelectFilter::make('Curriculum', 'version')
        ->options($versionOptions),
    ];
  }
}
