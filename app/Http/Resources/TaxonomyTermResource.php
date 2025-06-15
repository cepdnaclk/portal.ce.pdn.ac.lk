<?php

namespace App\Http\Resources;

// use App\Domains\Auth\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxonomyTermResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $metadata = $this->getFormattedMetadataAttribute();

        return [
            'code' => $this->code,
            'name' => $this->name,
            'terms' => $this->when(
                sizeof($this->children) > 0,
                TaxonomyTermResource::collection($this->children)
            ),
            'metadata' => $this->when(
                sizeof($metadata) > 0,
                $metadata
            ),
            // 'created_by' => User::find($this->created_by)?->name,
            // 'updated_by' => User::find($this->updated_by)?->name,
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
        ];
    }
}