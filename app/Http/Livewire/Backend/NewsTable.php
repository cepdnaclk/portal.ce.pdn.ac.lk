<?php

namespace App\Http\Livewire\Backend;

use App\Domains\News\Models\News;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class NewsTable extends DataTableComponent
{
    public array $perPageAccepted = [25, 50, 100];
    public bool $perPageAll = true;


    public function columns(): array
    {
        return [
            Column::make("Title", "title")
                ->sortable(),
            Column::make("Created At", "created_at")
                ->sortable(),
            Column::make("Updated At", "updated_at")
                ->sortable(),
            Column::make("Description", "description")
                ->searchable(),
            Column::make("Image", "image"),
            Column::make("Author", "author")
                ->searchable(),
            Column::make("Link URL", "link_url"),
            Column::make("Link Caption", "link_caption"),
            Column::make("Enabled", "enabled")
                ->searchable(),
            Column::make("Actions")
        ];
    }

    public function query(): Builder
    {
        return News::query()
            ->when($this->getFilter('area'), fn ($query, $status) => $query->where('area', $status))
            ->when($this->getFilter('type'), fn ($query, $type) => $query->where('type', $type));
    }

    // public function filters(): array
    // {
    //     $type = ["" => "Any"];
    //     foreach (News::types() as $key => $value) {
    //         $type[$key] = $value;
    //     }
    //     $area = ["" => "Any"];
    //     foreach (News::areas() as $key => $value) {
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
        return 'backend.news.index-table-row';
    }
}
