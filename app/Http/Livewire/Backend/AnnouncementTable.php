<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Announcement\Models\Announcement;
use App\Domains\Tenant\Services\TenantResolver;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;

class AnnouncementTable extends DataTableComponent
{
  public array $perPageAccepted = [25, 50, 100];
  public bool $perPageAll = true;

  public string $defaultSortColumn = 'starts_at';
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
      Column::make("Display Area", "area")
        ->sortable(),
      Column::make("Type", "type")
        ->sortable(),
      Column::make("Message", "message")
        ->searchable(),
      Column::make("Enabled", "enabled")
        ->sortable()
        ->format(function (Announcement $announcement) {
          return view('backend.announcement.enabled-toggle', ['announcement' => $announcement]);
        }),
      Column::make("Tenant", "tenant.slug")
        ->sortable(),
      Column::make("Start", "starts_at")
        ->sortable(),
      Column::make("End", "ends_at")
        ->sortable(),
      Column::make("Actions")
    ];
  }

  public function query(): Builder
  {
    $tenantIds = $this->getAvailableTenantIds();

    if (! $tenantIds) {
      return Announcement::query()->whereRaw('1 = 0');
    }

    return Announcement::query()
      ->with('tenant')
      ->when(! auth()->user()?->hasAllAccess(), function ($query) use ($tenantIds) {
        $query->whereIn('tenant_id', $tenantIds);
      })
      ->when($this->getFilter('tenant'), fn($query, $tenantId) => $query->where('tenant_id', $tenantId))
      ->when($this->getFilter('area'), fn($query, $status) => $query->where('area', $status))
      ->when($this->getFilter('type'), fn($query, $type) => $query->where('type', $type));
  }

  public function toggleEnable($announcementId)
  {
    abort_unless(auth()->user()?->can('user.access.editor.announcements'), 403);

    $tenantIds = $this->getAvailableTenantIds();
    $announcement = Announcement::query()
      ->when(! auth()->user()?->hasAllAccess(), function ($query) use ($tenantIds) {
        $query->whereIn('tenant_id', $tenantIds);
      })
      ->findOrFail($announcementId);
    $announcement->enabled = !$announcement->enabled;
    $announcement->save();
  }

  public function filters(): array
  {
    $tenants = $this->getAvailableTenants();
    $filters = [];

    if ($tenants->count() > 1) {
      $filters['tenant'] = Filter::make('Tenant')
        ->select(['' => 'Any'] + $tenants->pluck('name', 'id')->toArray());
    }

    $type = ["" => "Any"];
    foreach (Announcement::types() as $key => $value) {
      $type[$key] = $value;
    }
    $area = ["" => "Any"];
    foreach (Announcement::areas() as $key => $value) {
      $area[$key] = $value;
    }


    return array_merge($filters, [
      'area' => Filter::make('Display Area')
        ->select($area),
      'type' => Filter::make('Type')
        ->select($type),
    ]);
  }

  public function rowView(): string
  {
    return 'backend.announcements.index-table-row';
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