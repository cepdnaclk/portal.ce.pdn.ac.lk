<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmailDeliveryLogResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return array
   */
  public function toArray($request)
  {
    return [
      'id' => $this->id,
      'subject' => $this->subject,
      'to' => $this->to ?? [],
      'status' => $this->status,
      'sent_at' => $this->sent_at?->toIso8601String(),
      'metadata' => $this->metadata ?? [],
    ];
  }
}
