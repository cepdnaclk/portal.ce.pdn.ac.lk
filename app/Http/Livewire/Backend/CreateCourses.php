<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Domains\Course\Models\Course;
use App\Domains\Semester\Models\Semester;


class CreateCourses extends Component
{
    public $formStep = 1;

    //for slectors 
    public $academicProgramsList = ['Undergraduate','Postgraduate'];
    public $semestersList = [];
    public $curriculumsList = ['Current Curriculum','Curriculum - Effective from E22']; //later take this array from Course Model::getVersions()

    //form inputs
    //1st form step
    public $academicProgram;
    public $semester;
    public $version;
    public $type;
    public $code;
    public $name;
    public $credits,$faq_page,$content;
    public $time_allocation = [
        'lecture' => 0,
        'tutorial' => 0,
        'practical' => 0,
        'assignment' => 0,
    ];
    public $marks_allocation = [
        'practicals' => 0,
        'project' => 0,
        'mid_exam' => 0,
        'end_exam' => 0,
    ];

    //2nd form step
    public $objectives;
    public $ilos = [
        'knowledge' => [],
        'skills' => [],
        'attitudes' => [],
    ];
    
    //3rd form step
    public $references = [];

    public $rules = [
        'academicProgram' => 'required|string',
        'semester' => 'required|string',
        'version' => 'required|string',
        'type' => 'required|string',
        'code' => 'required|string',
        'name' => 'required|string',
        'credits' => 'required|integer|min:0',
        'faq_page' => 'nullable|url',
        'content' => 'nullable|string',
        'time_allocation.lecture' => 'required|integer|min:0',
        'time_allocation.tutorial' => 'required|integer|min:0',
        'time_allocation.practical' => 'required|integer|min:0',
        'time_allocation.assignment' => 'required|integer|min:0',
        'marks_allocation.practicals' => 'required|integer|min:0',
        'marks_allocation.project' => 'required|integer|min:0',
        'marks_allocation.mid_exam' => 'required|integer|min:0',
        'marks_allocation.end_exam' => 'required|integer|min:0',
        'objectives' => 'required|string',
        'ilos' => 'required|array|min:1',
        'references' => 'nullable|array'
    ];

    protected $listeners = ['itemsUpdated' => 'updateItems'];

    public function updateItems($type,$newItems){
        //dd($newItems);
        if($type == 'references'){
            $this->$type = $newItems;
        }else{
            $this->ilos[$type] = $newItems;
        }
        
        $this->emit('refreshItems' . ucfirst($type), $newItems);
    }

    public function updatedTimeAllocation($value, $key)
    {
        // You can add validation or other logic here
        // For example, ensure the value is non-negative
        $this->time_allocation[$key] = max(0, $value);
    }

    public function updatedMarksAllocation($value, $key)
    {
        // You can add validation or other logic here
        // For example, ensure the value is non-negative
        $this->marks_allocation[$key] = max(0, $value);
    }
    
    public function next(){
        //dump($this->ilos);
        $this->formStep++;
    }
    
    public function previous(){
        //dump($this->references);
        $this->formStep--;
    }

    public function submit(){
        // Validate the form data
        $this->validate();
        // Log the validated data
        try {
            // Create and save the course in the database
            $this->storeCourse();
            Log::info('Course created successfully.');
            $this->resetForm();
            session()->flash('success', 'Course created successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'There was an error creating the course.');
        }
    }

    public function updatedacademicProgram($value)
    {
        switch ($value) {
            case 'Postgraduate':
                $this->semestersList = Semester::where('academic_program', 'postgraduate')->pluck('title');
                break;
            case 'Undergraduate':
                $this->semestersList = Semester::where('academic_program', 'undergraduate')->pluck('title');
                break;
            default:
                $this->semestersList = [];
                break;
        }
    }

    protected function storeCourse()
    {
        Course::create([
            'academic_program' => $this->academicProgram,
            'semester_id' => $this->semester,
            'version' => $this->version,
            'type' => $this->type,
            'code' => $this->code,
            'name' => $this->name,
            'credits' => $this->credits,
            'faq_page' => $this->faq_page,
            'content' => $this->content,
            'time_allocation' => json_encode($this->time_allocation),
            'marks_allocation' => json_encode($this->marks_allocation),
            'objectives' => $this->objectives,
            'ilos' => json_encode($this->ilos),
            'references' => json_encode($this->references),
        ]);
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
        $this->time_allocation = [
            'lecture' => 0,
            'tutorial' => 0,
            'practical' => 0,
            'assignment' => 0,
        ];
        $this->marks_allocation = [
            'practicles' => 0,
            'project' => 0,
            'mid_exam' => 0,
            'end_exam' => 0,
        ];
        $this->objectives = '';
        $this->ilos = [
            'knowledge' => [],
            'skills' => [],
            'attitudes' => [],
        ];
        $this->references = [];
    }



    public function render()
    {
        return view('livewire.backend.create-courses');
    }
}