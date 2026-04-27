<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Taxonomy\Models\TaxonomyTerm;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class TaxonomyTermTable extends PersistentStateDataTable
{
  protected $model = TaxonomyTerm::class;

  public function configure(): void
  {
    parent::configure();

    $this->setDefaultSort('code', 'asc');
    $this->setPerPage(100);
    $this->setPerPageAccepted([10, 25, 50, 100, -1]);
  }

  public $taxonomy;

  public function mount($taxonomy)
  {
    $this->taxonomy = $taxonomy;
  }

  protected function getCookieContextKey(): string
  {
    return (string) ($this->taxonomy->id ?? 'unknown');
  }

  public function columns(): array
  {
    return [
      Column::make("Name", "name")
        ->searchable()->sortable(),
      Column::make("Code", "code")
        ->searchable()->sortable(),
      Column::make("Taxonomy Parent", "parent_id")
        ->format(function ($value, TaxonomyTerm $term) {
          if ($term->parent_id !== null && $term->parent) {
            return new HtmlString('<a href="?filters[taxonomy_term]=' . $term->parent->id . '" class="text-decoration-none">' . e($term->parent->name) . '</a>');
          }

          return 'N/A';
        })
        ->html(),
      Column::make("Created by", "created_by")
        ->format(fn($value, TaxonomyTerm $term) => $term->user_created->name ?? 'N/A')
        ->sortable(),
      Column::make("Updated by", "updated_by")
        ->format(fn($value, TaxonomyTerm $term) => $term->user_updated->name ?? 'N/A')
        ->sortable(),
      Column::make("Created at", "created_at")
        ->sortable(),
      Column::make("Updated at", "updated_at")
        ->sortable(),
      Column::make("API", 'code')
        ->excludeFromColumnSelect()
        ->format(fn($value, TaxonomyTerm $term) => $term->taxonomy->visibility
          ? new HtmlString('<a target="_blank" href="' . route('api.taxonomy.term.get', ['term_code' => $term->code]) . '">/' . $term->code . '</a>')
          : '-')
        ->html(),
      Column::make("Actions")
        ->excludeFromColumnSelect()
        ->format(function ($value, TaxonomyTerm $term) {
          $canEdit = auth()->user()?->hasPermissionTo('user.access.taxonomy.data.editor');
          $buttons = '<div class="d-flex px-0 mt-0 mb-0 justify-content-end">';
          $buttons .= '<div class="btn-group me-3" role="group" aria-label="View Buttons">';

          if ($term->children()->count() > 0) {
            $buttons .= '<a href="?filters[taxonomy_term]=' . $term->id . '" class="btn btn-sm btn-primary"><i class="fa fa-filter" title="Filter"></i></a>';
          }

          $buttons .= '<a href="' . route('dashboard.taxonomy.terms.history', ['taxonomy' => $term->taxonomy_id, 'term' => $term->id]) . '" class="btn btn-sm btn-info"><i class="fa fa-clock" title="History"></i></a>';
          $buttons .= '</div>';

          if ($canEdit) {
            $buttons .= '<div class="btn-group" role="group" aria-label="Edit Buttons">';
            $buttons .= '<a href="' . route('dashboard.taxonomy.terms.edit', ['taxonomy' => $term->taxonomy_id, 'term' => $term->id]) . '" class="btn btn-sm btn-warning"><i class="fa fa-pencil" title="Edit"></i></a>';
            $buttons .= '<a href="' . route('dashboard.taxonomy.terms.delete', ['taxonomy' => $term->taxonomy_id, 'term' => $term->id]) . '" class="btn btn-sm btn-danger"><i class="fa fa-trash" title="Delete"></i></a>';
            $buttons .= '</div>';
          }

          $buttons .= '</div>';

          return new HtmlString($buttons);
        })
        ->html()
    ];
  }

  public function builder(): Builder
  {
    return TaxonomyTerm::query()
      ->where('taxonomy_id', $this->taxonomy->id)
      ->when($this->getAppliedFilterWithValue('taxonomy_term'), fn($query, $type) => $query->where('parent_id', $type)->orWhere('id', $type))
      ->with('user')->orderBy('parent_id');
  }

  public function filters(): array
  {
    $terms = [];
    foreach (
      TaxonomyTerm::query()
        ->where('taxonomy_id', $this->taxonomy->id)->get() as $value
    ) {
      $terms[$value->id] = $value->name;
    };


    return [
      SelectFilter::make('Taxonomy Term', 'taxonomy_term')
        ->options($terms)
    ];
  }
}
