<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;
use Livewire\WithPagination;
use App\Domains\AcademicProgram\Course\Models\Course;

class PrerequisiteSelector extends Component
{
  use WithPagination;

  public $courseId;
  public $semester;
  public $academic_program;
  public $version;
  public $searchTerm = '';
  public $selectedCourses = [];
  protected $paginationTheme = 'bootstrap';

  protected $listeners = ['courseSelected', 'courseRemoved'];

  public function updatedSearchTerm()
  {
    $this->resetPage();
  }

  public function courseSelected($courseId)
  {
    $course = Course::find($courseId);
    if ($course && !in_array($courseId, collect($this->selectedCourses)->pluck('id')->toArray())) {
      $this->selectedCourses[] = $course->toArray();
      $this->emit('prerequisitesUpdated', $this->selectedCourses);
    }
  }

  public function courseRemoved($courseId)
  {
    $this->selectedCourses = collect($this->selectedCourses)->reject(function ($course) use ($courseId) {
      return $course['id'] == $courseId;
    })->values()->toArray();

    $this->emit('prerequisitesUpdated', $this->selectedCourses);
  }

  public function mount($courseId = null, $prerequisites = null)
  {
    $this->courseId = $courseId;
    $this->selectedCourses = $prerequisites;


    if ($this->courseId != null) {
      // Fetch existing prerequisites from the database
      $course = Course::with('prerequisites')->find($this->courseId);

      if ($course && $course->prerequisites) {
        // Assuming prerequisites is a relation on the Course model
        $this->selectedCourses = $course->prerequisites->map(function ($prerequisite) {
          return $prerequisite->toArray();
        })->toArray();
      }
    }
  }

  public function render()
  {
    $filteredAvailableCourses = Course::where(function ($query) {
      $query->where('code', 'like', '%' . $this->searchTerm . '%')
        ->orWhere('name', 'like', '%' . $this->searchTerm . '%');
    })
      ->whereNotIn('id', collect($this->selectedCourses)->pluck('id'))
      ->where('id', '!=', $this->courseId)
      ->where('academic_program', $this->academic_program)
      ->where('version', $this->version)
      ->paginate(5);

    return view('livewire.backend.prerequisite-selector', [
      'filteredAvailableCourses' => $filteredAvailableCourses,
    ]);
  }
}