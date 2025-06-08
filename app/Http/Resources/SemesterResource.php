<?php

namespace App\Http\Resources;

use App\Domains\AcademicProgram\Semester\Models\Semester;
use Illuminate\Http\Resources\Json\JsonResource;

class SemesterResource extends JsonResource
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
            'academic_program' => [
                'category' => $this->academic_program,
                'version' => $this->version,
                'curriculum_name' => $this->curriculum()
            ],
            'description' => $this->description,
            'url' => $this->url,
            'courses_count' => $this->when(isset($this->courses_count), $this->courses_count),
            // 'created_by' => User::find($this->created_by)?->name,
            // 'updated_by' => User::find($this->updated_by)?->name,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
