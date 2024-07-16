<?php

namespace App\Http\Controllers\Backend;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Domains\Event\Models\Event;
use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;

class EventApiController extends Controller
{
    public function upcoming()
    {
        $perPage = 20;
        $event = Event::getUpcomingEvents()
                    ->orderBy('start_at', 'asc')
                    ->paginate($perPage);

        if ($event->count() > 0) {
            return EventResource::collection($event);
        } else {
            return response()->json(['message' => 'News item not found'], 404);
        }
    }

    public function past()
    {
        $perPage = 20;
        $event = Event::getPastEvents()
                    ->orderBy('start_at', 'desc')
                    ->paginate($perPage);

        if ($event->count() > 0) {
            return EventResource::collection($event);
        } else {
            return response()->json(['message' => 'News item not found'], 404);
        }
    }


    public function show($id)
    {
        $event = Event::find($id);

        if ($event) {
            return new EventResource($event);
        } else {
            return response()->json(['message' => 'News item not found'], 404);
        }
    }
}
