<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Taxonomy\Models\Taxonomy;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\HtmlString;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filters\SelectFilter;

class TaxonomyTable extends PersistentStateDataTable
{
  protected $model = Taxonomy::class;

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
      Column::make("Code", "code")
        ->searchable()->sortable(),
      Column::make("Name", "name")
        ->searchable()->sortable(),
      Column::make("Created by", "created_by")
        ->format(fn($value, Taxonomy $taxonomy) => $taxonomy->user_created->name ?? 'N/A')
        ->sortable(),
      Column::make("Updated by", "updated_by")
        ->format(fn($value, Taxonomy $taxonomy) => $taxonomy->user_updated->name ?? 'N/A')
        ->sortable(),
      Column::make("Created at", "created_at")
        ->sortable(),
      Column::make("Updated at", "updated_at")
        ->sortable(),
      Column::make("API", 'code')
        ->excludeFromColumnSelect()
        ->format(fn($value, Taxonomy $taxonomy) => $taxonomy->visibility
          ? new HtmlString('<a target="_blank" href="' . route('api.taxonomy.get', ['taxonomy_code' => $taxonomy->code]) . '">/' . $taxonomy->code . '</a>')
          : '-')
        ->html(),
      Column::make("Actions")
        ->excludeFromColumnSelect()
        ->format(function ($value, Taxonomy $taxonomy) {
          $canEdit = auth()->user()?->hasPermissionTo('user.access.taxonomy.data.editor');
          $buttons = '<div class="d-flex px-0 mt-0 mb-0">';
          $buttons .= '<div class="btn-group me-3" role="group" aria-label="View Buttons">';
          $buttons .= '<a href="' . route('dashboard.taxonomy.terms.index', $taxonomy) . '" class="btn btn-sm btn-secondary"><i class="fa fa-list" title="Manage"></i></a>';
          $buttons .= '<a href="' . route('dashboard.taxonomy.history', $taxonomy) . '" class="btn btn-sm btn-info"><i class="fa fa-clock" title="History"></i></a>';
          $buttons .= '</div>';

          $buttons .= '<div class="btn-group" role="group" aria-label="Edit Buttons">';
          $buttons .= '<a href="' . route('dashboard.taxonomy.view', $taxonomy) . '" class="btn btn-sm btn-primary"><i class="fa fa-eye" title="View"></i></a>';

          if ($canEdit) {
            $buttons .= '<a href="' . route('dashboard.taxonomy.edit', $taxonomy) . '" class="btn btn-sm btn-warning"><i class="fa fa-pencil" title="Edit"></i></a>';
            $buttons .= '<a href="' . route('dashboard.taxonomy.delete', $taxonomy) . '" class="btn btn-sm btn-danger"><i class="fa fa-trash" title="Delete"></i></a>';
          }

          $buttons .= '</div></div>';

          return new HtmlString($buttons);
        })
        ->html(),
    ];
  }

  public function builder(): Builder
  {
    return Taxonomy::query()
      ->when($this->getAppliedFilterWithValue('visibility'), function ($query, $visible) {
        if ((string) $visible === '1') {
          $query->where('visibility', true);
        } elseif ((string) $visible === '2') {
          $query->where('visibility', 0);
        }
      })
      ->with('user');
  }

  public function filters(): array
  {
    return [
      SelectFilter::make('Visible to public', 'visibility')
        ->options([
          '' => 'Any',
          1 => 'Visible',
          2 => 'Hidden',
        ]),
    ];
  }
}
