<?php

namespace App\Http\Livewire\Backend;

use App\Domains\News\Models\News;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class NewsTable extends DataTableComponent
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
            Column::make("Author"),
            Column::make('Link Caption'),
            Column::make("Enabled", "enabled")
                ->sortable()
                ->format(function (News $news) {
                    return view('backend.news.enabled-toggle', ['news' => $news]);
                }),
            Column::make("Created At", "created_at")
                ->sortable(),
            Column::make("Updated At", "updated_at")
                ->sortable(),
            Column::make("Actions")
        ];
    }

    public function query(): Builder
    {
        return News::query()
            ->when($this->getFilter('area'), fn ($query, $status) => $query->where('area', $status))
            ->when($this->getFilter('type'), fn ($query, $type) => $query->where('type', $type));
    }

    public function toggleEnable($newsId)
    {
        $news = News::findOrFail($newsId);
        $news->enabled = !$news->enabled;
        $news->save();
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
