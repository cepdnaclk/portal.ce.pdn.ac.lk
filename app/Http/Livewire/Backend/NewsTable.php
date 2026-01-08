<?php

namespace App\Http\Livewire\Backend;

use App\Domains\ContentManagement\Models\News;
use App\Domains\Tenant\Services\TenantResolver;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class NewsTable extends PersistentStateDataTable
{
  public array $perPageAccepted = [5, 10, 20, 50];
  public bool $perPageAll = true;
  public int $perPage = 10;

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
      Column::make("Title", "title")
        ->searchable(),
      Column::make("Image", "image")->format(function (News $news) {
        return $news->thumbURL();
      }),
      Column::make("Description", "description"),
      Column::make("Enabled", "enabled")
        ->sortable()
        ->format(function (News $news) {
          return view('backend.news.enabled-toggle', ['news' => $news]);
        }),
      Column::make("Tenant", "tenant.slug")
        ->sortable(),
      Column::make("Author", "user.name")
        ->sortable()
        ->searchable(),
      Column::make("Published at", "published_at")
        ->sortable()->format(function (News $news) {
          return $news->published_at->format('yyyy-mm-dd');
        }),
      Column::make("Actions")
    ];
  }

  public function query(): Builder
  {
    $tenantIds = $this->getAvailableTenantIds();

    if (! $tenantIds) {
      return News::query()->whereRaw('1 = 0');
    }

    return News::query()
      ->with('tenant')
      ->when(! auth()->user()?->hasAllAccess(), function ($query) use ($tenantIds) {
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
      })->orderBy('published_at', 'desc');;
  }


  public function toggleEnable($newsId)
  {
    $tenantIds = $this->getAvailableTenantIds();
    $news = News::query()
      ->when(! auth()->user()?->hasAllAccess(), function ($query) use ($tenantIds) {
        $query->whereIn('tenant_id', $tenantIds);
      })
      ->findOrFail($newsId);
    $news->enabled = !$news->enabled;
    $news->save();
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
    return 'backend.news.index-table-row';
  }

  private function getAvailableTenants()
  {
    return app(TenantResolver::class)
      ->availableTenantsForUser(auth()->user())
      ->sortBy('slug')
      ->values();
  }

  private function getAvailableTenantIds(): array
  {
    return $this->getAvailableTenants()->pluck('id')->all();
  }
}