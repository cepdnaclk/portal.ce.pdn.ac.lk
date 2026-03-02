<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Email\Models\PortalApp;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class PortalAppTable extends PersistentStateDataTable
{
  public array $perPageAccepted = [5, 10, 25, 50];
  public bool $perPageAll = true;
  public int $perPage = 10;

  public string $defaultSortColumn = 'name';
  public string $defaultSortDirection = 'asc';

  public function columns(): array
  {
    return [
      Column::make(__('Name'), 'name')
        ->searchable()
        ->sortable(),
      Column::make(__('Status'), 'status')
        ->sortable(),
      Column::make(__('API Keys')),
      Column::make(__('Actions')),
    ];
  }

  public function query(): Builder
  {
    return PortalApp::query()
      ->withCount(['activeKeys', 'expiredKeys', 'revokedKeys'])
      ->when($this->getFilter('status'), function ($query, $status) {
        $query->where('status', $status);
      });
  }

  public function filters(): array
  {
    return [
      'status' => Filter::make(__('Status'))
        ->select([
          '' => __('Any'),
          PortalApp::STATUS_ACTIVE => __('Active'),
          PortalApp::STATUS_REVOKED => __('Revoked'),
        ]),
    ];
  }

  public function toggleStatus($portalAppId)
  {
    abort_unless(auth()->user()?->can('user.access.services'), 403);

    $portalApp = PortalApp::query()->findOrFail($portalAppId);
    $portalApp->status = $portalApp->status === PortalApp::STATUS_ACTIVE
      ? PortalApp::STATUS_REVOKED
      : PortalApp::STATUS_ACTIVE;
    $portalApp->save();
  }

  public function rowView(): string
  {
    return 'backend.portal-apps.index-table-row';
  }
}
