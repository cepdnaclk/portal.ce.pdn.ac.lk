<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Email\Models\ApiKey;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;

class PortalAppApiKeysTable extends PersistentStateDataTable
{
  public array $perPageAccepted = [5, 10, 25, 50];
  public bool $perPageAll = true;
  public int $perPage = 10;

  public string $defaultSortColumn = 'created_at';
  public string $defaultSortDirection = 'desc';

  public $portalApp;

  public function mount($portalApp): void
  {
    $this->portalApp = $portalApp;
  }

  protected function getCookieContextKey(): string
  {
    return (string) ($this->portalApp->id ?? 'unknown');
  }

  public function columns(): array
  {
    return [
      Column::make(__('Key Prefix'), 'key_prefix')
        ->searchable()
        ->sortable(),
      Column::make(__('Created'), 'created_at')
        ->sortable(),
      Column::make(__('Last Used'), 'last_used_at')
        ->sortable(),
      Column::make(__('Expires'), 'expires_at')
        ->sortable(),
      Column::make(__('Revoked'), 'revoked_at')
        ->sortable(),
      Column::make(__('Actions')),
    ];
  }

  public function query(): Builder
  {
    return ApiKey::query()
      ->where('portal_app_id', $this->portalApp->id)
      ->orderByDesc('created_at');
  }

  public function rowView(): string
  {
    return 'backend.portal-apps.keys.index-table-row';
  }
}