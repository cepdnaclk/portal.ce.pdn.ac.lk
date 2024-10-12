<?php

namespace App\Livewire\Backend;

use App\Domains\Announcement\Models\Announcement;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\{BooleanColumn, DateColumn};
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class AnnouncementTable extends DataTableComponent
{
    protected $model = Announcement::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setFooterStatus(true);
    }

    public function columns(): array
    {
        return [

            Column::make('id')->sortable(),
            Column::make("Display Area", 'area')
                ->format(fn($value) => Announcement::areas()[$value])
                ->sortable(),
            Column::make("Type", 'type')
                ->format(fn($value) => Announcement::types()[$value])
                ->sortable(),
            Column::make("Message", "message")
                ->searchable(),
            BooleanColumn::make("Enabled", 'enabled'),
            DateColumn::make("Start", "starts_at")
                ->inputFormat('Y-m-d H:i:s')
                ->outputFormat('Y-m-d')
                ->sortable(),
            DateColumn::make("End", "ends_at")
                ->inputFormat('Y-m-d H:i:s')
                ->outputFormat('Y-m-d')
                ->sortable(),
            Column::make('Actions')
                ->label(
                    fn($row, Column $column) => view('livewire.backend.action-column')->with([
                        'editLink' => route('dashboard.announcements.edit', $row),
                        'deleteLink' => route('dashboard.announcements.delete', $row),
                    ])
                ),
        ];
    }

    public function query(): Builder
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
        $type = ["" => "Any"];
        foreach (Announcement::types() as $key => $value) {
            $type[$key] = $value;
        }
        $area = ["" => "Any"];
        foreach (Announcement::areas() as $key => $value) {
            $area[$key] = $value;
        }

        return [
            SelectFilter::make('Display Area', 'area')
                ->options($area)
                ->filter(function (Builder $builder, string $value) {
                    $builder->where('area', $value);
                }),
            SelectFilter::make('Type')
                ->options($type),
        ];
    }
}