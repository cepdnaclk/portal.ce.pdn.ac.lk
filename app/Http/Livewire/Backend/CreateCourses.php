<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;

class CreateCourses extends Component
{
    public $formStep = 1;
    public function render()
    {
        return view('livewire.backend.create-courses');
    }

    public function next(){
        $this->formStep++;
    }

    public function previous(){
        $this->formStep--;
    }
}
