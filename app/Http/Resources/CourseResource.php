<?php

namespace App\Http\Resources;

use App\Domains\Auth\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'content' => $this->content,
            'credits' => $this->credits,
            'type' => $this->type,
            'semester_id' => $this->semester_id,
            'academic_program' => $this->academic_program,
            'version' => $this->version,
            'teaching_methods' => $this->teaching_methods,
            'objectives' => $this->objectives,
            'time_allocation' => $this->time_allocation,
            'marks_allocation' => $this->marks_allocation,
            'ilos' => $this->ilos,
            'references' => $this->references,
            'modules' => $this->whenLoaded('modules'),
            'created_by' => User::find($this->created_by)?->name,
            'updated_by' => User::find($this->updated_by)?->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}