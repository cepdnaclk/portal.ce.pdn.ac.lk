<?php

namespace App\Http\Resources;

use App\Domains\Taxonomy\Models\Taxonomy;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxonomyListResource extends JsonResource
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
            'api' => config('app.url', '') . "/api/taxonomy/v1/" . $this->code,
        ];
    }
}