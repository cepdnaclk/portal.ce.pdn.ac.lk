<?php

namespace App\Http\Resources;

use App\Domains\Auth\Models\User;
use App\Domains\Event\Models\Event;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $eventTypeList = Event::eventTypeMap();

        if ($this->event_type != null) {
            $eventTypes = array_map(function ($id) use ($eventTypeList) {
                return isset($eventTypeList[$id]) ? $eventTypeList[$id] : null;
            }, $this->event_type);
        } else {
            $eventTypes = [];
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'author' => User::find($this->created_by)->name,
            'image' =>  URL::to($this->thumbURL()),
            'start_at' => $this->start_at,
            'end_at' => $this->end_at,
            'event_type' => $eventTypes,
            'location' => $this->location,
            'link_url' => $this->link_url,
            'link_caption' => $this->link_caption,
            'posted_at' => $this->created_at,
            'updated_at' => $this->updated_at,

        ];
    }
}