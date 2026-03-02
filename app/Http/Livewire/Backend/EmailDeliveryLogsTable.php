<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Email\Models\EmailDeliveryLog;
use App\Domains\Email\Models\PortalApp;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class EmailDeliveryLogsTable extends PersistentStateDataTable
{
  public array $perPageAccepted = [10, 20, 50, 100];
  public bool $perPageAll = true;
  public int $perPage = 20;

  public string $defaultSortColumn = 'created_at';
  public string $defaultSortDirection = 'desc';

  public function columns(): array
  {
    return [
      Column::make(__('ID'), 'id')
        ->searchable()
        ->sortable(),
      Column::make(__('Portal App'), 'portalApp.name')
        ->searchable(),
      Column::make(__('Subject'), 'subject')
        ->searchable(),
      Column::make(__('Recipients')),
      Column::make(__('Status'), 'status')
        ->sortable(),
      Column::make(__('Sent At'), 'sent_at')
        ->sortable(),
      Column::make(__('Created'), 'created_at')
        ->sortable(),
    ];
  }

  public function query(): Builder
  {
    return EmailDeliveryLog::query()
      ->with('portalApp')
      ->when($this->getFilter('portal_app'), function ($query, $portalAppId) {
        $query->where('portal_app_id', $portalAppId);
      })
      ->when($this->getFilter('status'), function ($query, $status) {
        $query->where('status', $status);
      })
      // ->when($this->getFilter('from_date') !== null, function ($query, $fromDate) {
      //   dd($fromDate);
      //   $query->whereDate('created_at', '>=', $fromDate);
      // })
      // ->when($this->getFilter('to_date') !== null, function ($query, $toDate) {
      //   $query->whereDate('created_at', '<=', $toDate);
      // })
      ->orderByDesc('created_at');
  }

  public function filters(): array
  {
    $portalApps = PortalApp::query()->orderBy('name')->pluck('name', 'id')->toArray();

    return [
      'portal_app' => Filter::make(__('Portal App'))
        ->select(['' => __('All')] + $portalApps),
      'status' => Filter::make(__('Status'))
        ->select([
          '' => __('All'),
          EmailDeliveryLog::STATUS_QUEUED => __('Queued'),
          EmailDeliveryLog::STATUS_SENT => __('Sent'),
          EmailDeliveryLog::STATUS_FAILED => __('Failed'),
        ]),
      // 'from_date' => Filter::make(__('From Date'))
      //   ->date(),
      // 'to_date' => Filter::make(__('To Date'))
      //   ->date(),
    ];
  }

  public function rowView(): string
  {
    return 'backend.portal-apps.email-service.index-table-row';
  }
}
