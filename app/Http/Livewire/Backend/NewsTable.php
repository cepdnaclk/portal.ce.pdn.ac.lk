<?php

namespace App\Http\Livewire\Backend;

use App\Domains\News\Models\News;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class NewsTable extends DataTableComponent
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
            Column::make("Enabled", "enabled")
                ->sortable()
                ->format(function (News $news) {
                    return view('backend.news.enabled-toggle', ['news' => $news]);
                }),
            Column::make("Author"),
            Column::make("Actions")
        ];
    }

    public function query(): Builder
    {
        return News::query()
            ->when($this->getFilter('enabled') !== null, function ($query) {
                $enabled = $this->getFilter('enabled');
                if ($enabled === 1) {
                    $query->where('enabled', true);
                } elseif ($enabled === 0) {
                    $query->where('enabled', false);
                }
            });
    }


    public function toggleEnable($newsId)
    {
        $news = News::findOrFail($newsId);
        $news->enabled = !$news->enabled;
        $news->save();
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
        return 'backend.news.index-table-row';
    }
}
