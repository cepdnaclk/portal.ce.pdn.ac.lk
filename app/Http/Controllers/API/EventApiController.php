<?php

namespace App\Http\Controllers\API;

use App\Domains\Event\Models\Event;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use Illuminate\Support\Facades\Log;

class EventApiController extends Controller
{
    public function index()
    {
        Log::debug('Entering EventApiController@index');
        $perPage = 20;
        $event = Event::where('enabled', 1)->orderBy('start_at', 'desc')
            ->paginate($perPage);

        Log::debug('Events fetched', ['count' => $event->count()]);

        if ($event->count() > 0) {
            Log::info('Returning events', ['count' => $event->count()]);
            return EventResource::collection($event);
        } else {
            Log::warning('No events found');
            return response()->json(['message' => 'Events not found'], 404);
        }
    }

    public function upcoming()
    {
        Log::debug('Entering EventApiController@upcoming');
        $perPage = 20;
        $event = Event::getUpcomingEvents()
            ->orderBy('start_at', 'asc')
            ->paginate($perPage);

        Log::debug('Upcoming events fetched', ['count' => $event->count()]);

        if ($event->count() > 0) {
            Log::info('Returning upcoming events', ['count' => $event->count()]);
            return EventResource::collection($event);
        } else {
            Log::warning('No upcoming events found');
            return response()->json(['message' => 'Events not found'], 404);
        }
    }

    public function past()
    {
        Log::debug('Entering EventApiController@past');
        $perPage = 20;
        $event = Event::getPastEvents()
            ->orderBy('start_at', 'desc')
            ->paginate($perPage);

        Log::debug('Past events fetched', ['count' => $event->count()]);

        if ($event->count() > 0) {
            Log::info('Returning past events', ['count' => $event->count()]);
            return EventResource::collection($event);
        } else {
            Log::warning('No past events found');
            return response()->json(['message' => 'Events not found'], 404);
        }
    }

    public function show($id)
    {
        Log::debug('Entering EventApiController@show', ['id' => $id]);
        $event = Event::find($id);

        if ($event) {
            Log::info('Event found', ['id' => $id]);
            return new EventResource($event);
        } else {
            Log::warning('Event not found', ['id' => $id]);
            return response()->json(['message' => 'Event not found'], 404);
        }
    }
}