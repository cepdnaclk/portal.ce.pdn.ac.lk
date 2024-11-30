<?php

namespace App\Http\Resources;

use App\Domains\Auth\Models\User;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Http\Resources\TaxonomyTermResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxonomyResource extends JsonResource
{
    public $collects = Taxonomy::class;

    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'code' => $this->code,
            'name' => $this->name,
            'description' => $this->description,
            // 'properties' => $this->properties,
            'terms' => TaxonomyTermResource::collection($this->first_child_terms),
            // 'created_by' => User::find($this->created_by)?->name,
            // 'updated_by' => User::find($this->updated_by)?->name,
            // 'created_at' => $this->created_at,
            // 'updated_at' => $this->updated_at,
        ];
    }
}