<?php

namespace App\Http\Livewire\Backend;

use App\Domains\AcademicProgram\Course\Models\Course;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\{ButtonGroupColumn, LinkColumn};
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class CourseTable extends PersistentStateDataTable
{
  protected $model = Course::class;

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
      Column::make("Code", "code")
        ->searchable()
        ->sortable(),
      Column::make("Name", "name")
        ->searchable()
        ->sortable(),
      Column::make("Semester", 'semester_id')
        ->format(fn($value, Course $course) => $course->semester->title ?? '')
        ->excludeFromColumnSelect(),
      Column::make("Academic Program", "academic_program")
        ->format(fn($value, Course $course) => $course->academicProgram())
        ->sortable(),
      Column::make("Type", "type")
        ->sortable(),
      Column::make("Curriculum", "version")
        ->format(fn($value, Course $course) => $course->version())
        ->sortable(),
      Column::make("Credits", "credits")
        ->searchable(),
      Column::make("Updated by", "updated_by")
        ->format(fn($value, Course $course) => $course->updatedUser->name ?? '')
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
            ->location(fn($row) => route('dashboard.courses.edit', $row))
            ->attributes(fn() => [
              'class' => 'btn btn-info btn-xs',
            ])->html(),
          LinkColumn::make('Delete')
            ->title(fn() => '<i class="fa fa-trash"></i>')
            ->location(fn($row) => route('dashboard.courses.delete', $row))
            ->attributes(fn() => [
              'class' => 'btn btn-danger btn-xs',
            ])->html(),
        ])
        ->excludeFromColumnSelect(),
    ];
  }

  public function builder(): Builder
  {
    return Course::query()
      ->when($this->getAppliedFilterWithValue('academic_program'), fn($query, $type) => $query->where('academic_program', $type))
      ->when($this->getAppliedFilterWithValue('type'), fn($query, $type) => $query->where('type', $type))
      ->when($this->getAppliedFilterWithValue('version'), fn($query, $version) => $query->where('version', $version));
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
      SelectFilter::make('Academic Program', 'academic_program')
        ->options($academicProgramOptions),
      SelectFilter::make('Type', 'type')
        ->options($typeOptions),
      SelectFilter::make('Curriculum', 'version')
        ->options($versionOptions),
    ];
  }
}
