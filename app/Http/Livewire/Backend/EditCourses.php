<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;
use Illuminate\Validation\Rule;
use App\Domains\AcademicProgram\Course\Models\Course;
use App\Domains\AcademicProgram\Course\Models\CourseModule;
use App\Domains\AcademicProgram\Semester\Models\Semester;

class EditCourses extends Component
{
  public $course;
  public $formStep = 1;
  public $canUpdate = true;

  // Selectors
  public $academicProgramsList = [];
  public $semestersList = [];
  public $curriculumList = [];

  // Form inputs
  // 1st form step
  public $academicProgram;
  public $semester;
  public $version;
  public $type;
  public $code;
  public $name;
  public $credits, $faq_page, $content;
  public $time_allocation;
  public $module_time_allocation;
  public $marks_allocation;
  public $teaching_methods;

  // 2nd form step
  public $objectives;
  public $prerequisites = [];
  public $ilos = [
    'knowledge' => [],
    'skills' => [],
    'attitudes' => [],
  ];

  // 3rd form step
  public $references = [];
  public $modules = [];

  public function rules()
  {
    $validationRules = [
      'academicProgram' => 'required|string',
      'semester' => 'required|int',
      'version' => ['required', Rule::in(array_keys(Course::getVersions()))],
      'type' => ['required', 'string', Rule::in(array_keys(Course::getTypes()))],
      'code' => 'required|string',
      'name' => 'required|string|max:255',
      'credits' => 'required|integer|min:1|max:18',
      'teaching_methods' => 'nullable|string',
      'faq_page' => 'nullable|url',
      'content' => 'nullable|string',
      'modules' => 'nullable|array',
      'modules.*.name' => 'required|string|max:255',
      'modules.*.description' => 'nullable|string',
      'modules.*.time_allocation.lectures' => 'nullable|integer|min:0',
      'modules.*.time_allocation.tutorials' => 'nullable|integer|min:0',
      'modules.*.time_allocation.practicals' => 'nullable|integer|min:0',
      'modules.*.time_allocation.assignments' => 'nullable|integer|min:0',
    ];

    foreach (Course::getTimeAllocation() as $key => $value) {
      $validationRules["time_allocation.$key"] = 'nullable|integer|min:0';
      $validationRules["modules.*.time_allocation.$key"] = 'nullable|integer|min:0';
    }
    foreach (Course::getMarksAllocation() as $key => $value) {
      $validationRules["marks_allocation.$key"] = 'nullable|integer|min:0|max:100';
    }

    return $validationRules;
  }

  public function messages()
  {
    return [
      'academicProgram.required' => 'Please select an academic program.',
      'semester.required' => 'Please select a semester.',
      'version.required' => 'Please provide a curriculum.',
      'type.required' => 'Please select a course type.',
      'type.in' => 'The course type must be Foundation, Core, GE, or TE.',
      'code.required' => 'Please provide a course code.',
      'name.required' => 'Please provide a course name.',
      'credits.required' => 'Please specify the number of credits.',
      'credits.min' => 'The course must have at least 1 credit.',
      'credits.max' => 'The course cannot have more than 18 credits.',
      'modules.*.name.required' => 'Each module must have a name.',
      'modules.*.description.required' => 'Each module must have a description.',
      'modules.*.description.min' => 'Module descriptions should be at least 10 characters long.',
    ];
  }

  protected function validateCurrentStep()
  {
    switch ($this->formStep) {
      case 1:
        $this->validate($this->rules());
        $this->validateMarksAllocation();
        break;

      case 3:
        $this->validate([
          'modules' => 'nullable|array',
          'modules.*.name' => 'nullable|string|max:255',
          'modules.*.description' => 'nullable|string',
          'modules.*.time_allocation.lectures' => 'nullable|integer|min:0',
          'modules.*.time_allocation.tutorials' => 'nullable|integer|min:0',
          'modules.*.time_allocation.practicals' => 'nullable|integer|min:0',
          'modules.*.time_allocation.assignments' => 'nullable|integer|min:0',
        ]);
        break;
    }
  }

  protected function validateMarksAllocation()
  {
    $totalMarks = 0;
    $hasValue = false;

    foreach ($this->marks_allocation as $key => $value) {
      if (!empty($value)) {
        $hasValue = true;
        $totalMarks += (int) $value;
      }
    }

    if ($hasValue && $totalMarks != 100) {
      $this->addError('marks_allocation.total', 'The total of marks allocation must be 100.');
    }
  }

  public function updated($propertyName)
  {
    $this->canUpdate = false;

    if (!($this->version == null || $this->semester == null)) {
      // This to allow fillings while either version or semester is null
      $this->validateCurrentStep();
    }

    if ($this->getErrorBag()->has('marks_allocation.total')) {
      return;
    }
    $this->canUpdate = true;
  }

  protected $listeners = ['itemsUpdated' => 'updateItems', 'prerequisitesUpdated' => 'updatePrerequisites'];

  public function mount(Course $course)
  {
    $this->academicProgramsList = Course::getAcademicPrograms();
    $this->time_allocation = Course::getTimeAllocation();
    $this->module_time_allocation = Course::getTimeAllocation();
    $this->marks_allocation = Course::getMarksAllocation();
    $this->course = $course;

    // Populate form fields with existing course data
    $this->academicProgram = $course->academic_program;
    $this->semester = $course->semester_id;
    $this->version = $course->version;
    $this->type = $course->type;
    $this->code = $course->code;
    $this->name = $course->name;
    $this->credits = $course->credits;
    $this->teaching_methods = $course->teaching_methods;
    $this->faq_page = $course->faq_page;
    $this->content = $course->content;
    $this->time_allocation = array_merge(Course::getTimeAllocation(), json_decode($course->time_allocation, true));
    $this->marks_allocation = array_merge(Course::getMarksAllocation(), json_decode($course->marks_allocation, true));
    $this->objectives = $course->objectives;
    $this->ilos = array_merge(Course::getILOTemplate(), json_decode($course->ilos, true) ?? []);
    $this->references = json_decode($course->references, true) ?? [];

    // Load modules
    $this->modules = $course->modules()->get()->map(function ($module, $index) {
      return [
        'id' => $index + 1, // or use $module->id if available
        'name' => $module->topic,
        'description' => $module->description,
        'time_allocation' => array_merge(Course::getTimeAllocation(), json_decode($module->time_allocation, true))
      ];
    })->toArray();

    // Update the prerequisites list
    $this->prerequisites = $course->prerequisites;

    // Update semesters list based on academic program and version
    $this->updateSemestersList();

    // Update curriculum list based on academic program
    $this->updateCurriculumList();
  }

  public function updatePrerequisites($selectedCourses)
  {
    $this->prerequisites = $selectedCourses;
  }

  public function updateItems($type, $newItems)
  {
    if ($type == 'references') {
      $this->$type = $newItems;
    } else {
      $this->ilos[$type] = $newItems;
    }

    $this->emit('refreshItems' . ucfirst($type), $newItems);
  }


  public function next()
  {
    $this->validateCurrentStep();
    if ($this->getErrorBag()->has('marks_allocation.total')) {
      return; // Do not proceed to the next step if the marks total is invalid
    }
    $this->formStep++;
  }

  public function previous()
  {
    $this->formStep--;
  }

  public function update()
  {
    try {
      $this->validateCurrentStep();
      $this->updateCourse();
      return redirect()->route('dashboard.courses.index')->with('Success', 'Course updated successfully.');
    } catch (\Exception $e) {
      \Log::error("Error in update method: " . $e->getMessage());
      session()->flash('error', 'There was an error updating the course: ' . $e->getMessage());
    }
    $this->resetForm();
  }

  public function updatedAcademicProgram()
  {
    $this->updateCurriculumList();
    $this->updateSemestersList();
  }

  public function updatedVersion()
  {
    $this->updateSemestersList();
  }

  public function updateCurriculumList()
  {
    if ($this->academicProgram) {
      $this->curriculumList = Course::getVersions($this->academicProgram);
    } else {
      $this->curriculumList = [];
    }
    if (!array_key_exists($this->version, $this->curriculumList)) {
      // Unset if it not belongs to
      $this->version  = '';
    }
  }

  public function updateSemestersList()
  {
    if ($this->academicProgram && $this->version) {
      $this->semestersList = Semester::where('academic_program', $this->academicProgram)
        ->where('version', $this->version)
        ->pluck('title', 'id')
        ->toArray();
    } else {
      $this->semestersList = [];
    }

    if (count($this->semestersList) == 0 || !array_key_exists($this->semester, $this->semestersList)) {
      // Unset if it not belongs to
      $this->semester = '';
    }
  }

  protected function updateCourse()
  {

    try {
      \DB::beginTransaction();

      $course = Course::where('id', $this->course->id)->firstOrFail();

      $course->update([
        'academic_program' => $this->academicProgram,
        'semester_id' => (int)$this->semester,
        'version' => (int)$this->version,
        'type' => $this->type,
        'code' => $this->code,
        'name' => $this->name,
        'credits' => (int)$this->credits,
        'teaching_methods' => $this->teaching_methods,
        'faq_page' => $this->faq_page,
        'content' => $this->content,
        'time_allocation' => json_encode($this->time_allocation),
        'marks_allocation' => json_encode($this->marks_allocation),
        'objectives' => $this->objectives,
        'ilos' => json_encode($this->ilos),
        'references' => json_encode($this->references),
        'updated_by' => auth()->id()
      ]);


      $course->modules()->delete(); // Delete existing modules before adding new ones

      if (!empty($this->modules)) {
        foreach ($this->modules as $module) {
          CourseModule::create([
            'course_id' => $course->id,
            'topic' => $module['name'],
            'description' => $module['description'],
            'time_allocation' => json_encode($module['time_allocation']),
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
          ]);
        }
      }

      // Sync prerequisites
      if (!empty($this->prerequisites)) {
        $course->prerequisites()->sync(collect($this->prerequisites)->pluck('id')->toArray());
      } else {
        // If no prerequisites, detach all
        $course->prerequisites()->detach();
      }
      \DB::commit();
    } catch (\Exception $e) {
      \DB::rollBack();
      \Log::error("Error in updateCourse method: " . $e->getMessage());
      throw $e;
    }
  }

  protected function resetForm()
  {
    $this->formStep = 1;
    $this->academicProgram = '';
    $this->semester = '';
    $this->version = '';
    $this->type = '';
    $this->code = '';
    $this->name = '';
    $this->credits = null;
    $this->teaching_methods = '';
    $this->faq_page = '';
    $this->content = '';
    $this->time_allocation = Course::getTimeAllocation();
    $this->marks_allocation = Course::getMarksAllocation();
    $this->module_time_allocation = Course::getTimeAllocation();
    $this->objectives = '';
    $this->ilos = Course::getILOTemplate();
    $this->references = [];
    $this->modules = [];
    $this->prerequisites = [];
  }

  public function render()
  {
    return view('livewire.backend.edit-courses');
  }
}