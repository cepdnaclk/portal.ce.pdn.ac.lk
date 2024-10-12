<?php

namespace App\Livewire\Backend;

use App\Domains\Announcement\Models\Announcement;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class AnnouncementTable extends DataTableComponent
{
    protected $model = Announcement::class;

    // public array $perPageAccepted = [25, 50, 100];
    // public bool $perPageAll = true;
    // public string $defaultSortDirection = 'desc';

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setFooterStatus(true);
    }

    public function columns(): array
    {
        $this->setDefaultSort('starts_at');

        return [
            Column::make("Display Area", "area")
                ->sortable(),
            Column::make("Type", "type")
                ->sortable(),
            Column::make("Message", "message")
                ->searchable(),
            Column::make("Enabled", "enabled")
                ->label(
                    fn($row, Column $column) => 'xxx'
                )->html(),
            // ->component('backend.announcement.enabled-toggle')->attributes(fn($value, $row, Column $column) => [
            //     'announcement' => $row->id
            // ])
            // ->sortable(),
            // ->format(function (Announcement $announcement) {
            //     return view('backend.announcement.enabled-toggle', ['announcement' => $announcement]);
            // }),
            Column::make("Start", "starts_at")
                ->sortable(),
            Column::make("End", "ends_at")
                ->sortable(),
            Column::make("Actions")->label(
                fn($row, Column $column) => 'xxx'
            )->html(),
        ];
    }

    // public function builder()
    // {
    //     return Announcement::query();
    // }

    // public function query(): Builder
    // {
    //     return Announcement::query()
    //         ->when($this->getFilter('area'), fn($query, $status) => $query->where('area', $status))
    //         ->when($this->getFilter('type'), fn($query, $type) => $query->where('type', $type));
    // }

    public function toggleEnable($announcementId)
    {
        $announcement = Announcement::findOrFail($announcementId);
        $announcement->enabled = !$announcement->enabled;
        $announcement->save();
    }

    public function builder(): Builder
    {
        return Announcement::query()
            // ->with() // Eager load anything
            // ->join() // Join some tables
            ->select(); // Select some things
    }


    // public function filters(): array
    // {
    //     $type = ["" => "Any"];
    //     foreach (Announcement::types() as $key => $value) {
    //         $type[$key] = $value;
    //     }
    //     $area = ["" => "Any"];
    //     foreach (Announcement::areas() as $key => $value) {
    //         $area[$key] = $value;
    //     }

    //     return [
    //         'area' => Filter::make('Display Area')
    //             ->select($area),
    //         'type' => Filter::make('Type')
    //             ->select($type),
    //     ];
    // }

    // public function rowView(): string
    // {
    //     return 'backend.announcements.index-table-row';
    // }

    public function emptyMessage(): string
    {
        return 'No rows available';
    }
}