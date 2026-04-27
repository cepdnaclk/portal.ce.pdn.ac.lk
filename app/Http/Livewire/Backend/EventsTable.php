<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Event\Models\Event;
use App\Helpers\DescriptionHelper;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Carbon;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\{ButtonGroupColumn, ImageColumn, LinkColumn};
use Rappasoft\LaravelLivewireTables\Views\Filters\{SelectFilter, MultiSelectFilter};

class EventsTable extends PersistentStateDataTable
{
  protected $model = Event::class;

  public function configure(): void
  {
    parent::configure();
    $this->setAdditionalSelects(['start_at', 'end_at', 'created_at']);

    $this->setDefaultSort('start_at', 'desc');
  }

  public function columns(): array
  {
    return [
      Column::make("ID", "id")
        ->excludeFromColumnSelect()
        ->sortable(),
      Column::make("Title", "title")
        ->sortable()
        ->searchable(),
      ImageColumn::make("Image", "image")
        ->location(fn($row) => $row->thumbURL())
        ->attributes(fn() => [
          'class' => 'img-thumbnail',
          'style' => 'width: 120px; max-height: 120px;',
        ]),
      Column::make("Description", "description")
        ->format(function ($value, Event $event) {
          $desc = DescriptionHelper::process($event->description);
          return new HtmlString(mb_strlen($desc) > 256 ? mb_substr($desc, 0, 256) . '...' : $desc);
        })
        ->html(),
      Column::make("Enabled", "enabled")
        ->sortable()
        ->format(fn($value, Event $event) => view('components.backend.enabled_toggle', ['row' => $event])),

      Column::make("Start", "start_at")
        ->sortable()
        ->format(function ($value) {
          if (!$value) return '';
          $dt = Carbon::parse($value);
          return $dt->format('H:i:s') === '00:00:00'
            ? $dt->format('Y-m-d')
            : $dt->format('Y-m-d H:i');
        }),
      Column::make("End", "end_at")
        ->sortable()
        ->format(function ($value) {
          if (!$value) return '';
          $dt = Carbon::parse($value);
          return $dt->format('H:i:s') === '00:00:00'
            ? $dt->format('Y-m-d')
            : $dt->format('Y-m-d H:i');
        }),
      Column::make("Location", "location")
        ->searchable()
        // ->format(fn($value, Event $event) => new HtmlString('<div class="custom-width-1" style="width: 75px;">' . e($event->location) . '</div>'))
        // ->html(),
        ->format(function ($value, Event $event) {
          $loc = DescriptionHelper::process($event->location);
          return new HtmlString(mb_strlen($loc) > 64 ? mb_substr($loc, 0, 64) . '...' : $loc);
        })
        ->html(),
      Column::make("Author", 'created_by')
        ->format(fn($value, Event $event) => $event->user?->name ?? '')
        ->excludeFromColumnSelect(),
      ButtonGroupColumn::make('Actions')
        ->attributes(fn() => [
          'class' => 'btn-group',
          'role' => 'group',
        ])
        ->buttons([
          LinkColumn::make('Preview')
            ->title(fn() => '<i class="fa fa-eye"></i>')
            ->location(fn($row) => route('dashboard.event.preview', $row))
            ->attributes(fn() => [
              'class' => 'btn btn-warning btn-sm',
            ])->html(),
          LinkColumn::make('Edit')
            ->title(fn() => '<i class="fa fa-pencil"></i>')
            ->location(fn($row) => route('dashboard.event.edit', $row))
            ->attributes(fn() => [
              'class' => 'btn btn-info btn-sm',
            ])->html(),
          LinkColumn::make('Delete')
            ->title(fn() => '<i class="fa fa-trash"></i>')
            ->location(fn($row) => route('dashboard.event.delete', $row))
            ->attributes(fn() => [
              'class' => 'btn btn-danger btn-sm',
            ])->html(),
        ])
        ->excludeFromColumnSelect(),
    ];
  }

  public function builder(): Builder
  {
    return Event::query()
      ->when($this->getAppliedFilterWithValue('status') !== null, function ($query) {
        $status = $this->getAppliedFilterWithValue('status');
        if ((int) $status === 1) {
          $query->getUpcomingEvents();
        } elseif ((int) $status === 0) {
          $query->getPastEvents();
        }
      })
      ->when($this->getAppliedFilterWithValue('enabled') !== null, function ($query) {
        $enabled = $this->getAppliedFilterWithValue('enabled');
        if ((int) $enabled === 1) {
          $query->where('enabled', true);
        } elseif ((int) $enabled === 0) {
          $query->where('enabled', false);
        }
      })
      ->when($this->getAppliedFilterWithValue('event_type') !== null, function ($query) {
        $eventType = $this->getAppliedFilterWithValue('event_type');
        // event_type is a JSON array in DB, match if it contains the selected type
        $query->whereJsonContains('event_type', $eventType);
      });
  }

  public function toggleEnable($eventId)
  {
    $event = Event::findOrFail($eventId);
    $event->enabled = !$event->enabled;
    $event->save();
  }

  public function filters(): array
  {
    return [
      SelectFilter::make('Enabled', 'enabled')
        ->options([
          '' => 'Any',
          1 => 'Enabled',
          0 => 'Not Enabled',
        ]),
      SelectFilter::make('Status', 'status')
        ->options([
          '' => 'Any',
          1 => 'Upcoming',
          0 => 'Past',
        ]),
      MultiSelectFilter::make("Event Type", 'event_type')
        ->options(Event::eventTypeMap())
    ];
  }
}