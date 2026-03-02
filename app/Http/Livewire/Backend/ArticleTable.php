<?php

namespace App\Http\Livewire\Backend;

use App\Support\Concerns\ResolvesAvailableTenants;
use App\Domains\ContentManagement\Models\Article;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class ArticleTable extends PersistentStateDataTable
{
  use ResolvesAvailableTenants;

  protected string $tenantSortColumn = 'slug';

  public array $perPageAccepted = [25, 50, 100];
  public bool $perPageAll = true;
  public int $perPage = 25;

  public string $defaultSortColumn = 'published_at';
  public string $defaultSortDirection = 'desc';

  public function mount(): void
  {
    $tenantIds = $this->getAvailableTenantIds();
    if (count($tenantIds) === 1 && empty($this->filters['tenant'])) {
      $this->filters['tenant'] = $tenantIds[0];
    }
  }

  public function columns(): array
  {
    return [
      Column::make('Title', 'title')
        ->searchable(),
      Column::make('Categories'),
      Column::make('Content'),
      Column::make('Enabled', 'enabled')
        ->sortable()
        ->format(function (Article $article) {
          return view('components.backend.enabled-toggle', ['row' => $article]);
        }),
      Column::make('Tenant', 'tenant.name'),
      Column::make('Author', 'author.name')
        ->sortable()
        ->searchable(),
      Column::make('Published at', 'published_at')
        ->sortable(),
      Column::make('Actions'),
    ];
  }

  public function query(): Builder
  {
    $tenantIds = $this->getAvailableTenantIds();

    if (! $tenantIds) {
      return Article::query()->whereRaw('1 = 0');
    }

    return Article::query()
      ->with(['tenant', 'author'])
      ->when(! auth()->user()->hasAllAccess(), function ($query) use ($tenantIds) {
        $query->whereIn('tenant_id', $tenantIds);
      })
      ->when($this->getFilter('tenant'), function ($query, $tenantId) {
        $query->where('tenant_id', $tenantId);
      })
      ->when($this->getFilter('enabled') !== null, function ($query) {
        $enabled = $this->getFilter('enabled');
        if ($enabled === 1) {
          $query->where('enabled', true);
        } elseif ($enabled === 0) {
          $query->where('enabled', false);
        }
      })
      ->orderBy('published_at', 'desc');
  }

  public function toggleEnable($articleId)
  {
    $tenantIds = $this->getAvailableTenantIds();
    $article = Article::query()
      ->when(! auth()->user()?->hasAllAccess(), function ($query) use ($tenantIds) {
        $query->whereIn('tenant_id', $tenantIds);
      })
      ->findOrFail($articleId);
    $article->enabled = ! $article->enabled;
    $article->save();
  }

  public function filters(): array
  {
    $tenants = $this->getAvailableTenants();
    $filters = [];

    if ($tenants->count() > 1) {
      $filters['tenant'] = Filter::make('Tenant')
        ->select(['' => 'Any'] + $tenants->pluck('name', 'id')->toArray());
    }

    return array_merge($filters, [
      'enabled' => Filter::make('Enabled')
        ->select([
          '' => 'Any',
          1 => 'Enabled',
          0 => 'Not Enabled',
        ]),
    ]);
  }

  public function rowView(): string
  {
    return 'backend.article.index-table-row';
  }
}
