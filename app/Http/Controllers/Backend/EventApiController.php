<?php

namespace App\Http\Controllers\Backend;

use App\Http\Resources\EventResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domains\EventItem\Models\EventItem;

class EventApiController extends Controller
{
    public function index()
    {
        $perPage = 20; 
        $event = EventItem::latest()->paginate($perPage);

        if ($event->count() > 0) {
            return EventResource::collection($event);
        } else {
            return response()->json(['message' => 'News item not found'], 404);
        }
    }


    public function show($id)
    {
        $event = EventItem::find($id);

        if ($event) {
            return new EventResource($event);
        } else {
            return response()->json(['message' => 'News item not found'], 404);
        }
    }
}
