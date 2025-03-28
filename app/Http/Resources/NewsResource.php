<?php

namespace App\Http\Resources;

use App\Domains\Auth\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\URL;

class NewsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'author' => User::find($this->created_by)->name,
            'image' =>  URL::to($this->thumbURL()),
            'link_url' => $this->link_url,
            'link_caption' => $this->link_caption,
            'published_at' => $this->published_at,
            'updated_at' => $this->updated_at,

        ];
    }
}