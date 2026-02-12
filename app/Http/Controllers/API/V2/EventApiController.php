<?php

namespace App\Http\Controllers\API\V2;

use App\Domains\ContentManagement\Models\Event;
use App\Domains\Tenant\Services\TenantResolver;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventApiController extends Controller
{
  public function __construct(private TenantResolver $tenantResolver) {}

  public function index(Request $request, $tenant_slug)
  {
    try {
      $tenant = $this->tenantResolver->resolveBySlug($tenant_slug);

      if (! $tenant) {
        return response()->json(['message' => 'Tenant not found'], 404);
      }

      $query = Event::with(['gallery', 'author'])
        ->where('enabled', 1)
        ->forTenant($tenant)
        ->orderBy('start_at', 'desc');

      if ($request->has('event_type')) {
        if (in_array($request->event_type, Event::eventTypeMap())) {
          $eventTypeId = array_search($request->event_type, Event::eventTypeMap());

          // Note: This is not the best way, but easiest way to filter JSON content
          $query = $query->where('event_type', 'LIKE', "%\"$eventTypeId\"%");
        } else {
          return EventResource::collection([]);
        }
      }

      $events = $query->paginate(20);

      return EventResource::collection($events);
    } catch (\Exception $e) {
      Log::error('Error in V2 EventApiController@index', $e);
      return response()->json(['message' => 'An error occurred while fetching events'], 500);
    }
  }

  public function upcoming($tenant_slug)
  {
    try {
      $tenant = $this->tenantResolver->resolveBySlug($tenant_slug);

      if (! $tenant) {
        return response()->json(['message' => 'Tenant not found'], 404);
      }

      $perPage = 20;
      $event = Event::with(['gallery', 'author'])
        ->forTenant($tenant)
        ->getUpcomingEvents()
        ->orderBy('start_at', 'asc')
        ->paginate($perPage);

      if ($event->count() > 0) {
        return EventResource::collection($event);
      }

      return response()->json(['message' => 'Events not found'], 404);
    } catch (\Exception $e) {
      Log::error('Error in V2 EventApiController@upcoming', ['error' => $e->getMessage()]);
      return response()->json(['message' => 'An error occurred while fetching upcoming events'], 500);
    }
  }

  public function past($tenant_slug)
  {
    try {
      $tenant = $this->tenantResolver->resolveBySlug($tenant_slug);

      if (! $tenant) {
        return response()->json(['message' => 'Tenant not found'], 404);
      }

      $perPage = 20;
      $event = Event::with(['gallery', 'author'])
        ->forTenant($tenant)
        ->getPastEvents()
        ->orderBy('start_at', 'desc')
        ->paginate($perPage);

      if ($event->count() > 0) {
        return EventResource::collection($event);
      }

      return response()->json(['message' => 'Events not found'], 404);
    } catch (\Exception $e) {
      Log::error('Error in V2 EventApiController@past', ['error' => $e->getMessage()]);
      return response()->json(['message' => 'An error occurred while fetching past events'], 500);
    }
  }

  public function show($tenant_slug, $id)
  {
    try {
      $tenant = $this->tenantResolver->resolveBySlug($tenant_slug);

      if (! $tenant) {
        return response()->json(['message' => 'Tenant not found'], 404);
      }

      $event = Event::with(['gallery', 'author'])->forTenant($tenant)->find($id);

      if ($event) {
        return new EventResource($event);
      }

      return response()->json(['message' => 'Event not found'], 404);
    } catch (\Exception $e) {
      Log::error('Error in V2 EventApiController@show', ['error' => $e->getMessage(), 'id' => $id]);
      return response()->json(['message' => 'An error occurred while fetching the event'], 500);
    }
  }
}
