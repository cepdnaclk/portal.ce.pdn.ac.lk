<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Event\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class EventsTable extends DataTableComponent
{
    public array $perPageAccepted = [5, 10, 20, 50];
    public bool $perPageAll = true;
    public int $perPage = 10;


    public function columns(): array
    {
        return [
            Column::make("Title", "title")
                ->sortable()
                ->searchable(),
            Column::make("Image", "image"),
            Column::make("Description", "description"),
            Column::make("Enabled", "enabled")
                ->sortable()
                ->format(function (Event $event) {
                    return view('backend.event.enabled-toggle', ['event' => $event]);
                }),
            Column::make("Time"),
            Column::make("Location", "location")
                ->searchable(),
            Column::make("Author"),
            Column::make("Actions")
        ];
    }

    public function query(): Builder
    {
        return Event::query()
            ->when($this->getFilter('status') !== null, function ($query) {
                $status = $this->getFilter('status');
                if ($status === 1) {
                    $query->getUpcomingEvents();
                } elseif ($status === 0) {
                    $query->getPastEvents();
                }
            })
            ->when($this->getFilter('enabled') !== null, function ($query) {
                $enabled = $this->getFilter('enabled');
                if ($enabled === 1) {
                    $query->where('enabled', true);
                } elseif ($enabled === 0) {
                    $query->where('enabled', false);
                }
            })->orderBy('published_at', 'desc');
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
            'enabled' => Filter::make('Enabled')
                ->select([
                    '' => 'Any',
                    1 => 'Enabled',
                    0 => 'Not Enabled',
                ]),
            'status' => Filter::make('Status')
                ->select([
                    '' => 'Any',
                    1 => 'Upcoming',
                    0 => 'Past',
                ]),
        ];
    }

    public function rowView(): string
    {
        return 'backend.event.index-table-row';
    }
}