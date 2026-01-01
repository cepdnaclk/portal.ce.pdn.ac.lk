<?php

namespace App\Http\Controllers\API;

use App\Domains\ContentManagement\Models\Event;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Domains\Tenant\Services\TenantResolver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class EventApiController extends Controller
{
  public function __construct(private TenantResolver $tenantResolver) {}

  public function index(Request $request)
  {
    try {
      $tenant = $this->tenantResolver->resolveDefault();

      if (! $tenant) {
        return EventResource::collection(collect());
      }

      $query = Event::with('gallery')
        ->where('enabled', 1)
        ->forTenant($tenant)
        ->orderBy('start_at', 'desc');

      if ($request->has('event_type')) {

        if (in_array($request->event_type, Event::eventTypeMap())) {
          $eventTypeId = array_search($request->event_type, Event::eventTypeMap());

          // Note: This is not the best way, but easiest way to filter JSON content
          $query = $query->where('event_type', 'LIKE', "%\"$eventTypeId\"%");
        } else {
          return  EventResource::collection([]);
        }
      }

      $events = $query->paginate(20);

      return EventResource::collection($events);
    } catch (\Exception $e) {
      Log::error('Error in EventApiController@index', $e);
      return response()->json(['message' => 'An error occurred while fetching events'], 500);
    }
  }

  public function upcoming()
  {
    try {
      $tenant = $this->tenantResolver->resolveDefault();
      if (! $tenant) {
        return response()->json(['message' => 'Events not found'], 404);
      }

      $perPage = 20;
      $event = Event::with('gallery')
        ->forTenant($tenant)
        ->getUpcomingEvents()
        ->orderBy('start_at', 'asc')
        ->paginate($perPage);

      if ($event->count() > 0) {
        return EventResource::collection($event);
      } else {
        return response()->json(['message' => 'Events not found'], 404);
      }
    } catch (\Exception $e) {
      Log::error('Error in EventApiController@upcoming', ['error' => $e->getMessage()]);
      return response()->json(['message' => 'An error occurred while fetching upcoming events'], 500);
    }
  }

  public function past()
  {
    try {
      $tenant = $this->tenantResolver->resolveDefault();
      if (! $tenant) {
        return response()->json(['message' => 'Events not found'], 404);
      }

      $perPage = 20;
      $event = Event::with('gallery')
        ->forTenant($tenant)
        ->getPastEvents()
        ->orderBy('start_at', 'desc')
        ->paginate($perPage);

      if ($event->count() > 0) {
        return EventResource::collection($event);
      } else {
        return response()->json(['message' => 'Events not found'], 404);
      }
    } catch (\Exception $e) {
      Log::error('Error in EventApiController@past', ['error' => $e->getMessage()]);
      return response()->json(['message' => 'An error occurred while fetching past events'], 500);
    }
  }

  public function show($id)
  {
    try {
      $tenant = $this->tenantResolver->resolveDefault();
      if (! $tenant) {
        return response()->json(['message' => 'Event not found'], 404);
      }

      $event = Event::with('gallery')->forTenant($tenant)->find($id);

      if ($event) {
        return new EventResource($event);
      } else {
        return response()->json(['message' => 'Event not found'], 404);
      }
    } catch (\Exception $e) {
      Log::error('Error in EventApiController@show', ['error' => $e->getMessage(), 'id' => $id]);
      return response()->json(['message' => 'An error occurred while fetching the event'], 500);
    }
  }
}