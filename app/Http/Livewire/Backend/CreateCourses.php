<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Domains\Course\Models\Course;
use App\Domains\Course\Models\CourseModule;
use App\Domains\Semester\Models\Semester;


class CreateCourses extends Component
{
    public $formStep = 1;

    //for slectors 
    public $academicProgramsList = [];
    public $semestersList = [];

    //form inputs
    //1st form step
    public $academicProgram;
    public $semester;
    public $version;
    public $type;
    public $code;
    public $name;
    public $credits,$faq_page,$content;
    public $time_allocation;
    public $marks_allocation;

    //2nd form step
    public $objectives;
    public $ilos = [
        'knowledge' => [],
        'skills' => [],
        'attitudes' => [],
    ];
    
    //3rd form step
    public $references = [];
    public $modules = [];

    public function rules()
    {
        return [
            'academicProgram' => 'required|string',
            'semester' => 'required|string',
            'version' => 'required|string',
            'type' => 'required|string|in:Core,GE,TE',
            'code' => 'required|string|unique:courses,code',
            'name' => 'required|string|max:255',
            'credits' => 'required|integer|min:1|max:30',
            'faq_page' => 'nullable|url',
            'content' => 'nullable|string',
            'time_allocation.lecture' => 'nullable|integer|min:0',
            'time_allocation.tutorial' => 'nullable|integer|min:0',
            'time_allocation.practical' => 'nullable|integer|min:0',
            'time_allocation.assignment' => 'nullable|integer|min:0',
            'marks_allocation.practicals' => 'nullable|integer|min:0|max:100',
            'marks_allocation.project' => 'nullable|integer|min:0|max:100',
            'marks_allocation.mid_exam' => 'nullable|integer|min:0|max:100',
            'marks_allocation.end_exam' => 'nullable|integer|min:0|max:100',
            'modules' => 'nullable|array',
            'modules.*.name' => 'required|string|min:3|max:255',
            'modules.*.description' => 'required|string|min:10',
            'modules.*.time_allocation.lectures' => 'nullable|integer|min:0',
            'modules.*.time_allocation.tutorials' => 'nullable|integer|min:0',
            'modules.*.time_allocation.practicals' => 'nullable|integer|min:0',
            'modules.*.time_allocation.assignments' => 'nullable|integer|min:0',
        ];
    }

    public function messages()
    {
        return [
            'academicProgram.required' => 'Please select an academic program.',
            'semester.required' => 'Please select a semester.',
            'version.required' => 'Please provide a version.',
            'type.required' => 'Please select a course type.',
            'type.in' => 'The course type must be core, elective, or optional.',
            'code.required' => 'Please provide a course code.',
            'code.unique' => 'This course code is already in use.',
            'name.required' => 'Please provide a course name.',
            'credits.required' => 'Please specify the number of credits.',
            'credits.min' => 'The course must have at least 1 credit.',
            'credits.max' => 'The course cannot have more than 30 credits.',
            'modules.*.name.required' => 'Each module must have a name.',
            'modules.*.description.required' => 'Each module must have a description.',
            'modules.*.description.min' => 'Module descriptions should be at least 10 characters long.',
        ];
    }

    protected function validateCurrentStep()
{
    switch ($this->formStep) {
        case 1:
            $this->validate([
                'academicProgram' => 'required|string',
                'semester' => 'required|string',
                'version' => 'required|string',
                'type' => 'required|string|in:Core,GE,TE',
                'code' => 'required|string|unique:courses,code',
                'name' => 'required|string|max:255',
                'credits' => 'required|integer|min:1|max:30',
                'faq_page' => 'nullable|url',
                'content' => 'nullable|string',
                'time_allocation.lecture' => 'nullable|integer|min:0',
                'time_allocation.tutorial' => 'nullable|integer|min:0',
                'time_allocation.practical' => 'nullable|integer|min:0',
                'time_allocation.assignment' => 'nullable|integer|min:0',
                'marks_allocation.practicals' => 'nullable|integer|min:0|max:100',
                'marks_allocation.project' => 'nullable|integer|min:0|max:100',
                'marks_allocation.mid_exam' => 'nullable|integer|min:0|max:100',
                'marks_allocation.end_exam' => 'nullable|integer|min:0|max:100',
            ]);
            $this->validateMarksAllocation();
            if ($this->getErrorBag()->has('marks_allocation.total')) {
                return; 
            }
            break;

        case 3:
            $this->validate([
                'modules' => 'nullable|array',
                'modules.*.name' => 'required|string|min:3|max:255',
                'modules.*.description' => 'required|string',
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

        foreach ($this->marks_allocation as $key => $value){
            if(!empty($value)){
                $hasValue = true;
                $totalMarks += (int) $value;
            }
        }

        if ($hasValue && $totalMarks != 100){
            $this->addError('marks_allocation.total', 'The total of marks allocation must be 100.');
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    protected $listeners = ['itemsUpdated' => 'updateItems'];

    public function mount()
    {
        $this->academicProgramsList = Course::getAcademicPrograms();
        $this->time_allocation = Course::getTimeAllocation();
        $this->marks_allocation = Course::getMarksAllocation();
    }

    public function updateItems($type,$newItems){
        if($type == 'references'){
            $this->$type = $newItems;
        }else{
            $this->ilos[$type] = $newItems;
        }
    }
    
    public function next(){
        $this->validateCurrentStep();
        if ($this->getErrorBag()->has('marks_allocation.total')) {
            return; // Do not proceed to the next step if the marks total is invalid
        }
        $this->formStep++;
    }
    
    public function previous(){
        $this->formStep--;
    }

    public function submit(){
        \Log::info("Submit method called");
        try {
            $this->validate();
            $this->storeCourse();
            return redirect()->route('dashboard.courses.index')->with('Success', 'Course created successfully.');
        } catch (\Exception $e) {
            \Log::error("Error in submit method: " . $e->getMessage());
            session()->flash('error', 'There was an error creating the course: ' . $e->getMessage());
        }
        $this->resetForm();
    }
    
    public function updatedAcademicProgram()
    {
        $this->updateSemestersList();
    }

    public function updatedVersion()
    {
        $this->updateSemestersList();
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
    }


    protected function storeCourse()
    {
        \Log::info("storeCourse method called");
        
        try {
            \DB::beginTransaction();

            $course = Course::create([
                'academic_program' => $this->academicProgram,
                'semester_id' => (int)$this->semester,
                'version' => (int)$this->version,
                'type' => $this->type,
                'code' => $this->code,
                'name' => $this->name,
                'credits' => (int)$this->credits,
                'faq_page' => $this->faq_page,
                'content' => $this->content,
                'time_allocation' => json_encode($this->time_allocation),
                'marks_allocation' => json_encode($this->marks_allocation),
                'objectives' => $this->objectives,
                'ilos' => json_encode($this->ilos),
                'references' => json_encode($this->references),
                'created_by' => auth()->id(),
                'updated_by' => auth()->id()
            ]);

            \Log::info("Course created with ID: " . $course->id);

            if (empty($this->modules)) {
                \Log::warning("No modules to create");
            } else {
                foreach ($this->modules as $module) {
                    $createdModule = CourseModule::create([
                        'course_id' => $course->id,
                        'topic' => $module['name'],
                        'description' => $module['description'],
                        'time_allocation' => json_encode($module['time_allocation']),
                        'created_by' => auth()->id(),
                        'updated_by' => auth()->id(),
                    ]);
                    \Log::info("Created module with ID: " . $createdModule->id);
                }
            }

            \DB::commit();
            \Log::info("storeCourse method completed successfully");
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error("Error in storeCourse method: " . $e->getMessage());
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
        $this->credits = 0;
        $this->faq_page = '';
        $this->content = '';
        $this->time_allocation = Course::getTimeAllocation();
        $this->marks_allocation = Course::getMarksAllocation();
        $this->objectives = '';
        $this->ilos = [
            'knowledge' => [],
            'skills' => [],
            'attitudes' => [],
        ];
        $this->references = [];
        $this->modules = [];
    }

    public function render()
    {
        return view('livewire.backend.create-courses');
    }
}