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
        try {
            $perPage = 20;
            $event = Event::where('enabled', 1)->orderBy('start_at', 'desc')
                ->paginate($perPage);

            if ($event->count() > 0) {
                return EventResource::collection($event);
            } else {
                return response()->json(['message' => 'Events not found'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error in EventApiController@index', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'An error occurred while fetching events'], 500);
        }
    }

    public function upcoming()
    {
        try {
            $perPage = 20;
            $event = Event::getUpcomingEvents()
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
            $perPage = 20;
            $event = Event::getPastEvents()
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
            $event = Event::find($id);

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