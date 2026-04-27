<?php

namespace App\Http\Livewire\Backend;

use App\Domains\News\Models\News;
use App\Helpers\DescriptionHelper;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Support\HtmlString;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\{ButtonGroupColumn, LinkColumn, ImageColumn};
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class NewsTable extends PersistentStateDataTable
{
  protected $model = News::class;

  public function configure(): void
  {
    parent::configure();

    // Sorting
    $this->setDefaultSort('published_at', 'desc');
  }

  public function columns(): array
  {
    return [
      Column::make("ID", "id")
        ->excludeFromColumnSelect()
        ->sortable(),
      Column::make("Title", "title")
        ->searchable(),
      ImageColumn::make("Thumbnail", "thumbnail")
        ->location(
          fn($row) => $row->thumbURL()
        )->attributes(
          fn($row) => [
            'alt' => $row->title,
            'style' => 'width: 120px; height: 120px;',
          ]
        ),
      Column::make("Description", "description")
        ->format(function ($value, News $news) {
          $desc = DescriptionHelper::process($news->description);
          return new HtmlString(mb_strlen($desc) > 250 ? mb_substr($desc, 0, 250) . '...' : $desc);
        })
        ->html(),
      Column::make("Enabled", "enabled")
        ->sortable()
        ->format(fn($value, News $news) => view('components.backend.enabled_toggle', ['row' => $news])),
      Column::make("Author", "user.name")
        ->sortable()
        ->searchable(),
      Column::make("Published", "published_at")
        ->sortable(),
      ButtonGroupColumn::make('Actions')
        ->attributes(fn() => [
          'class' => 'btn-group',
          'role' => 'group',
        ])
        ->buttons([
          LinkColumn::make('Preview')
            ->title(fn() => '<i class="fa fa-eye"></i>')
            ->location(fn($row) => route('dashboard.news.preview', $row))
            ->attributes(fn() => [
              'class' => 'btn btn-warning btn-sm',
            ])->html(),
          LinkColumn::make('Edit')
            ->title(fn() => '<i class="fa fa-pencil"></i>')
            ->location(fn($row) => route('dashboard.news.edit', $row))
            ->attributes(fn() => [
              'class' => 'btn btn-info btn-sm',
            ])->html(),
          LinkColumn::make('Delete')
            ->title(fn() => '<i class="fa fa-trash"></i>')
            ->location(fn($row) => route('dashboard.news.delete', $row))
            ->attributes(fn() => [
              'class' => 'btn btn-danger btn-sm',
            ])->html(),
        ])
        ->excludeFromColumnSelect(),
    ];
  }

  public function builder(): Builder
  {
    return News::query()
      ->when($this->getAppliedFilterWithValue('enabled') !== null, function ($query) {
        $enabled = $this->getAppliedFilterWithValue('enabled');
        if ((int) $enabled === 1) {
          $query->where('enabled', true);
        } elseif ((int) $enabled === 0) {
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
      SelectFilter::make('Enabled', 'enabled')
        ->options([
          '' => 'Any',
          1 => 'Enabled',
          0 => 'Not Enabled',
        ]),
    ];
  }
}