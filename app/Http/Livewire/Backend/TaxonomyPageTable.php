<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Taxonomy\Models\TaxonomyPage;
use App\Domains\Taxonomy\Models\Taxonomy;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Support\HtmlString;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class TaxonomyPageTable extends PersistentStateDataTable
{
  protected $model = TaxonomyPage::class;

  public function configure(): void
  {
    parent::configure();

    $this->setDefaultSort('created_at', 'desc');
    $this->setPerPage(25);
    $this->setPerPageAccepted([10, 25, 50, 100, -1]);
  }

  public function columns(): array
  {
    return [
      Column::make('Slug', 'slug')->searchable()->sortable(),
      Column::make('Taxonomy', 'taxonomy.name')
        ->format(function ($value, TaxonomyPage $page) {
          if ($page->taxonomy) {
            return new HtmlString('<a href="' . route('dashboard.taxonomy.terms.index', $page->taxonomy) . '">' . e($page->taxonomy->name) . '</a>');
          }

          return '—';
        })
        ->html(),
      Column::make('Created by', 'created_by')
        ->format(fn($value, TaxonomyPage $page) => $page->user_created->name ?? 'N/A')
        ->sortable(),
      Column::make('Updated by', 'updated_by')
        ->format(fn($value, TaxonomyPage $page) => $page->user_updated->name ?? 'N/A')
        ->sortable(),
      Column::make('Created at', 'created_at')->sortable(),
      Column::make('Updated at', 'updated_at')->sortable(),
      Column::make('Actions')
        ->excludeFromColumnSelect()
        ->format(function ($value, TaxonomyPage $page) {
          $canEdit = auth()->user()?->hasPermissionTo('user.access.taxonomy.page.editor');
          $buttons = '<div class="d-flex px-0 mt-0 mb-0">';
          $buttons .= '<a href="' . route('download.taxonomy-page', ['slug' => $page->slug]) . '" class="btn btn-sm btn-secondary me-3" target="_blank"><i class="fa fa-globe" title="' . __('Web') . '"></i></a>';
          $buttons .= '<div class="btn-group" role="group" aria-label="' . __('Actions') . '">';
          $buttons .= '<a href="' . route('dashboard.taxonomy-pages.history', $page) . '" class="btn btn-sm btn-info"><i class="fa fa-clock" title="' . __('History') . '"></i></a>';
          $buttons .= '<a href="' . route('dashboard.taxonomy-pages.view', $page) . '" class="btn btn-sm btn-primary"><i class="fa fa-eye" title="' . __('Preview') . '"></i></a>';

          if ($canEdit) {
            $buttons .= '<a href="' . route('dashboard.taxonomy-pages.edit', $page) . '" class="btn btn-sm btn-warning"><i class="fa fa-pencil" title="' . __('Edit') . '"></i></a>';
            $buttons .= '<a href="' . route('dashboard.taxonomy-pages.delete', $page) . '" class="btn btn-sm btn-danger"><i class="fa fa-trash" title="' . __('Delete') . '"></i></a>';
          }

          $buttons .= '</div></div>';

          return new HtmlString($buttons);
        })
        ->html(),
    ];
  }

  public function builder(): Builder
  {
    return TaxonomyPage::query()
      ->with('taxonomy')
      ->when($this->getAppliedFilterWithValue('taxonomy_id'), fn($q, $id) => $q->where('taxonomy_id', $id));
  }

  public function filters(): array
  {
    $taxonomy = [];
    foreach (Taxonomy::query()->get() as $value) {
      $taxonomy[$value->id] = $value->name;
    }

    return [
      SelectFilter::make('Taxonomy', 'taxonomy_id')->options($taxonomy)
    ];
  }
}
