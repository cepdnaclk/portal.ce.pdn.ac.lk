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
      'academic_program' => [
        'category' => $this->academic_program,
        'version' => $this->version,
        'curriculum_name' => $this->curriculum()
      ],
      'teaching_methods' => $this->teaching_methods,
      'objectives' => $this->objectives,
      'time_allocation' => json_decode($this->time_allocation),
      'marks_allocation' => json_decode($this->marks_allocation),
      'ilos' => json_decode($this->ilos, true),
      'references' => json_decode($this->references),
      'modules' => $this->whenLoaded('modules', function () {
        return $this->modules->map(function ($module) {
          return [
            'topic' => $module->topic,
            'description' => $module->description,
            'time_allocation' => json_decode($module->time_allocation, true),
            'created_at' => $module->created_at,
            'updated_at' => $module->updated_at,
          ];
        });
      }),
      'prerequisites' =>  $this->prerequisites->map(function ($course) {
        return [
          'id' => $course->id,
          'code' => $course->code,
          'name' => $course->name,
          'urls' => [
            'view' => 'https://www.ce.pdn.ac.lk/courses/' . urlencode($course->academic_program) . '/' . urlencode($course->code),
            'edit' => route('dashboard.courses.edit', $course->id),
          ]
        ];
      }),
      'urls' => [
        'view' => 'https://www.ce.pdn.ac.lk/courses/' . urlencode($this->academic_program) . '/' . urlencode($this->code),
        'edit' => route('dashboard.courses.edit', $this->id),
      ],
      // 'created_by' => User::find($this->created_by)?->name,
      // 'updated_by' => User::find($this->updated_by)?->name,
      'created_at' => $this->created_at,
      'updated_at' => $this->updated_at,
    ];
  }
}