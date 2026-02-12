<?php

namespace App\Http\Livewire\Backend;

use App\Domains\ContentManagement\Models\Event;
use App\Domains\Tenant\Services\TenantResolver;
use Illuminate\Database\Eloquent\Builder;
use App\Http\Livewire\Components\PersistentStateDataTable;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Filter;
use Illuminate\Support\Facades\Cache;

class EventsTable extends PersistentStateDataTable
{
  public array $perPageAccepted = [5, 10, 20, 50];
  public bool $perPageAll = true;
  public int $perPage = 10;

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
        ->sortable()
        ->searchable(),
      Column::make("Image", "image"),
      Column::make("Description", "description"),
      Column::make("Enabled", "enabled")
        ->sortable()
        ->format(function (Event $event) {
          return view('backend.event.enabled-toggle', ['event' => $event]);
        }),
      Column::make("Tenant", "tenant.name"),
      Column::make("Time"),
      Column::make("Location", "location")
        ->searchable(),
      Column::make("Author", "author.name")
        ->sortable()
        ->searchable(),
      Column::make("Actions")
    ];
  }

  public function query(): Builder
  {
    $tenantIds = $this->getAvailableTenantIds();

    if (! $tenantIds) {
      // No events if no access to any tenant
      return Event::query()->whereRaw('1 = 0');
    }

    return Event::query()
      ->with(['tenant', 'author'])
      ->when(! auth()->user()?->hasAllAccess(), function ($query) use ($tenantIds) {
        $query->whereIn('tenant_id', $tenantIds);
      })
      ->when($this->getFilter('tenant'), function ($query, $tenantId) {
        $query->where('tenant_id', $tenantId);
      })
      ->when($this->getFilter('status') !== null, function ($query) {
        $status = $this->getFilter('status');
        if ($status === 1) {
          $query->getUpcomingEvents();
        } elseif ($status === 0) {
          $query->getPastEvents();
        }
      })
      ->when($this->getFilter('enabled') !== null, function ($query) {
        $enabled = $this->getFilter('enabled');
        if ($enabled === 1) {
          $query->where('enabled', true);
        } elseif ($enabled === 0) {
          $query->where('enabled', false);
        }
      })
      ->when(
        $this->getFilter('event_type') !== null,
        function ($query) {
          $eventType = $this->getFilter('event_type');
          $query->where('event_type', 'LIKE', "%\"$eventType\"%");
        }
      )
      ->orderBy('created_at', 'desc');
  }
  public function toggleEnable($eventId)
  {
    $tenantIds = $this->getAvailableTenantIds();
    $event = Event::query()
      ->when(! auth()->user()?->hasAllAccess(), function ($query) use ($tenantIds) {
        $query->whereIn('tenant_id', $tenantIds);
      })
      ->findOrFail($eventId);
    $event->enabled = !$event->enabled;
    $event->save();
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
      'status' => Filter::make('Status')
        ->select([
          '' => 'Any',
          1 => 'Upcoming',
          0 => 'Past',
        ]),
      'event_type' => Filter::make("Event Type")
        ->select(array_merge(['' => 'Any'], Event::eventTypeMap()))
    ]);
  }

  public function rowView(): string
  {
    return 'backend.event.index-table-row';
  }

  private function getAvailableTenants()
  {
    $cacheKey = 'events_table.tenants.user.' . (auth()->id() ?? 'guest');

    return Cache::remember($cacheKey, 60, function () {
      return app(TenantResolver::class)
        ->availableTenantsForUser(auth()->user())
        ->sortBy('slug')
        ->values();
    });
  }

  private function getAvailableTenantIds(): array
  {
    return $this->getAvailableTenants()->pluck('id')->all();
  }
}
