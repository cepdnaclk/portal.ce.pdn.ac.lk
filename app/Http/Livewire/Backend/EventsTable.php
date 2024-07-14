<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Event\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class EventsTable extends DataTableComponent
{
    public array $perPageAccepted = [4, 10, 100];
    public bool $perPageAll = true;


    public function columns(): array
    {
        return [
            Column::make("Title", "title")
                ->sortable()
                ->searchable(), 
            Column::make("Image", "image"),
            Column::make("Author"),
            Column::make('Link Caption'),
            Column::make("Enabled", "enabled")
                ->sortable()
                ->format(function (Event $event) {
                    return view('backend.event.enabled-toggle', ['event' => $event]);
                }),
            Column::make("Start Time", "start_at")
                ->searchable(),
            Column::make("End Time", "end_at")
                ->searchable(),
            Column::make("Location", "location")
                ->searchable(),
            Column::make("Created At", "created_at")
                ->sortable(),
            Column::make("Updated At", "updated_at")
                ->sortable(),
            Column::make("Actions")
        ];
    }

    public function query(): Builder
    {
        return Event::query()
            ->when($this->getFilter('enabled') !== null, function ($query) {
                $enabled = $this->getFilter('enabled');
                if ($enabled === 1) {
                    $query->where('enabled', true);
                } elseif ($enabled === 0) {
                    $query->where('enabled', false);
                }
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
            'enabled' => Filter::make('Enabled')
                ->select([
                    '' => 'Any',
                     1 => 'Enabled',
                     0 => 'Not Enabled',
                ]),
        ];
    }

    public function rowView(): string
    {
        return 'backend.event.index-table-row';
    }
}
