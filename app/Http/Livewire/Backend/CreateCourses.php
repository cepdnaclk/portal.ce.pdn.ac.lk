<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;

class CreateCourses extends Component
{
    public $formStep = 1;

    public $ilos = [];
    public $knowledge = [];
    public $skills = [];
    public $attitudes = [];
    public $references = [];

    protected $listeners = ['itemsUpdated' => 'updateItems'];

    public function updateItems($type,$newItems){
        $this->$type = $newItems;
        $this->emit('refreshItems' . ucfirst($type), $newItems);
    }

    
    public function next(){
        $this->formStep++;
    }
    
    public function previous(){
        $this->formStep--;
    }

    public function submit(){
    }

    public function render()
    {
        return view('livewire.backend.create-courses');
    }
}
