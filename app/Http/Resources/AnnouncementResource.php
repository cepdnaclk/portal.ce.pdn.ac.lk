<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AnnouncementResource extends JsonResource
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
      'area' => $this->area,
      'type' => $this->type,
      'message' => $this->message,
      'starts_at' => $this->starts_at,
      'ends_at' => $this->ends_at,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }
}