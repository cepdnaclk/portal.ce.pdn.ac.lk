<?php

namespace App\Http\Controllers\Backend;

use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domains\Event\Models\Event;

class EventApiController extends Controller
{
    public function index()
    {
        $perPage = 20;
        $event = Event::latest()->paginate($perPage);

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
