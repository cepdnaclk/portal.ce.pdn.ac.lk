<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyFile as ModelsTaxonomyFile;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Support\HtmlString;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class TaxonomyFileTable extends PersistentStateDataTable
{
  protected $model = ModelsTaxonomyFile::class;

  public function configure(): void
  {
    parent::configure();

    $this->setDefaultSort('created_at', 'desc');
    $this->setPerPage(100);
    $this->setPerPageAccepted([10, 25, 50, 100, -1]);
  }

  public function columns(): array
  {
    return [
      Column::make('File Name (Slug)', 'file_name')->searchable()->sortable(),
      Column::make('Taxonomy', 'taxonomy.name')
        ->format(function ($value, ModelsTaxonomyFile $file) {
          if ($file->taxonomy) {
            return new HtmlString('<a href="' . route('dashboard.taxonomy.terms.index', $file->taxonomy) . '">' . e($file->taxonomy->name) . '</a>');
          }

          return '—';
        })
        ->html(),
      Column::make("Created by", "created_by")
        ->format(fn($value, ModelsTaxonomyFile $file) => $file->user_created->name ?? 'N/A')
        ->sortable(),
      Column::make("Updated by", "updated_by")
        ->format(fn($value, ModelsTaxonomyFile $file) => $file->user_updated->name ?? 'N/A')
        ->sortable(),
      Column::make('Created at', 'created_at')->sortable(),
      Column::make('Updated at', 'updated_at')->sortable(),
      Column::make('Actions')
        ->excludeFromColumnSelect()
        ->format(function ($value, ModelsTaxonomyFile $file) {
          $canEdit = auth()->user()?->hasPermissionTo('user.access.taxonomy.file.editor');
          $buttons = '<div class="d-flex px-0 mt-0 mb-0">';
          $buttons .= '<a href="' . route('download.taxonomy-file', ['file_name' => $file->file_name, 'extension' => $file->getFileExtension()]) . '" class="btn btn-sm btn-secondary me-3" target="_blank"><i class="fa fa-download" title="' . __('Download') . '"></i></a>';
          $buttons .= '<div class="btn-group" role="group" aria-label="' . __('Actions') . '">';
          $buttons .= '<a href="' . route('dashboard.taxonomy-files.view', $file) . '" class="btn btn-sm btn-primary"><i class="fa fa-eye" title="' . __('View') . '"></i></a>';

          if ($canEdit) {
            $buttons .= '<a href="' . route('dashboard.taxonomy-files.edit', $file) . '" class="btn btn-sm btn-warning"><i class="fa fa-pencil" title="' . __('Edit') . '"></i></a>';
            $buttons .= '<a href="' . route('dashboard.taxonomy-files.delete', $file) . '" class="btn btn-sm btn-danger"><i class="fa fa-trash" title="' . __('Delete') . '"></i></a>';
          }

          $buttons .= '</div></div>';

          return new HtmlString($buttons);
        })
        ->html(),
    ];
  }

  public function builder(): Builder
  {
    return ModelsTaxonomyFile::query()
      ->with('taxonomy')
      ->when($this->getAppliedFilterWithValue('taxonomy_id'), fn($query, $type) => $query->where('taxonomy_id', $type));
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
