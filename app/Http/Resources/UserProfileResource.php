<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserProfileResource extends JsonResource
{
  /**
   * Transform the resource into an array.
   *
   * @param  \Illuminate\Http\Request  $request
   */
  public function toArray($request): array
  {
    return array_filter([
      // Identity
      'email' => $this->email,
      'alternate_email' => $this->alternate_email,
      'honorific' => $this->honorific,
      'full_name' => $this->full_name,
      'name_with_initials' => $this->name_with_initials,
      'preferred_short_name' => $this->preferred_short_name,
      'preferred_long_name' => $this->preferred_long_name,

      // Location & affiliation
      'location' => $this->location,
      'current_affiliation' => $this->current_affiliation,
      'current_position' => $this->current_position,
      'profile_image' => $this->profileImageUrl(),

      // Derived from the assigned profile types
      'designation' => $this->designation,
      'affiliation' => $this->affiliation,
      'interests' => $this->interests,

      'links' => $this->links->pluck('url', 'type')->toArray(),
    ], fn($value) => !is_null($value) && $value !== '' && $value !== []);
  }
}
