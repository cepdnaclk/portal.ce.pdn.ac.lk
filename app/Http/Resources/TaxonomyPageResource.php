<?php

namespace App\Http\Resources;

use App\Domains\Auth\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxonomyPageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'html' => $this->html,
            'taxonomy_id' => $this->taxonomy_id,
            'created_by' => User::find($this->created_by)?->name,
            'updated_by' => User::find($this->updated_by)?->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
