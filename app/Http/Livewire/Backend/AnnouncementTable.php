<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Announcement\Models\Announcement;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\{ButtonGroupColumn, LinkColumn};
use Rappasoft\LaravelLivewireTables\Views\Filters\{SelectFilter, MultiSelectFilter};

class AnnouncementTable extends PersistentStateDataTable
{
  protected $model = Announcement::class;

  public function configure(): void
  {
    parent::configure();

    // Sorting
    $this->setDefaultSort('starts_at', 'desc');
  }

  public function columns(): array
  {
    return [
      Column::make("ID", "id")
        ->excludeFromColumnSelect()
        ->sortable(),
      Column::make("Display Area", "area")
        ->format(
          fn($value, $row) => Announcement::areas()[$row->area]
        )
        ->sortable(),
      Column::make("Type", "type")
        ->format(
          fn($value, $row) => Announcement::types()[$row->type]
        )->sortable(),
      Column::make("Message", "message")
        ->searchable(),
      Column::make("Enabled", "enabled")
        ->sortable()
        ->format(function ($value, $row) {
          return view('backend.announcements.enabled-toggle', ['announcement' => $row]);
        }),
      Column::make("Start", "starts_at")
        ->sortable(),
      Column::make("End", "ends_at")
        ->sortable(),
      ButtonGroupColumn::make('Actions')
        ->attributes(function ($row) {
          return [
            'class' => 'btn-group',
            'role' => "group"
          ];
        })
        ->buttons(
          [
            LinkColumn::make('Edit')
              ->title(fn($row) => '<i class="fa fa-pencil"></i>')
              ->location(fn($row) => route('dashboard.announcements.edit', $row))
              ->attributes(function ($row) {
                return [
                  'class' => 'btn btn-info btn-sm',
                ];
              })->html(),
            LinkColumn::make('Delete')
              ->title(fn($row) => '<i class="fa fa-trash"></i>')
              ->location(fn($row) => route('dashboard.announcements.delete', $row))
              ->attributes(function ($row) {
                return [
                  'class' => 'btn btn-danger btn-sm',
                ];
              })->html(),
          ]
        )
        ->excludeFromColumnSelect(),
    ];
  }

  public function builder(): Builder
  {
    return Announcement::query()
      ->when($this->getAppliedFilterWithValue('area'), fn($query, $status) => $query->where('area', $status))
      ->when($this->getAppliedFilterWithValue('type'), fn($query, $type) => $query->where('type', $type));
  }


  public function toggleEnable($announcementId)
  {
    $announcement = Announcement::findOrFail($announcementId);
    $announcement->enabled = !$announcement->enabled;
    $announcement->save();
  }

  public function filters(): array
  {
    $area = ["" => "Any"];
    foreach (Announcement::areas() as $key => $value) {
      $area[$key] = $value;
    }
    $type = [];
    foreach (Announcement::types() as $key => $value) {
      $type[$key] = $value;
    }

    return [
      SelectFilter::make('Display Area', 'area')
        ->options($area),
      MultiSelectFilter::make('Type', 'type')
        ->options($type),
    ];
  }
}
