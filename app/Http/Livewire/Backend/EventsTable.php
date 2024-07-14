<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Event\Models\Event;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class EventsTable extends DataTableComponent
{
    public array $perPageAccepted = [5, 10, 20];
    public bool $perPageAll = true;
    public int $perPage = 5;


    public function columns(): array
    {
        return [
            Column::make("Title", "title")
                ->sortable()
                ->searchable(), 
            Column::make("Image", "image"),
            Column::make("Author", "author")
                ->searchable(),
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
            ->when($this->getFilter('area'), fn ($query, $status) => $query->where('area', $status))
            ->when($this->getFilter('type'), fn ($query, $type) => $query->where('type', $type));
    }

    public function toggleEnable($eventId)
    {
        $event = Event::findOrFail($eventId);
        $event->enabled = !$event->enabled;
        $event->save();
    }

    // public function filters(): array
    // {
    //     $type = ["" => "Any"];
    //     foreach (Event::types() as $key => $value) {
    //         $type[$key] = $value;
    //     }
    //     $area = ["" => "Any"];
    //     foreach (Event::areas() as $key => $value) {
    //         $area[$key] = $value;
    //     }

    //     return [
    //         'area' => Filter::make('Display Area')
    //             ->select($area),
    //         'type' => Filter::make('Type')
    //             ->select($type),
    //     ];
    // }

    public function rowView(): string
    {
        return 'backend.event.index-table-row';
    }
}
