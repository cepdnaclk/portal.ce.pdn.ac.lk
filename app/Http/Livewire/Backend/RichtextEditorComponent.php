<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;

class RichtextEditorComponent extends Component
{
    public $name;
    public $value;

    public function mount($name, $value = '')
    {
        $this->name = $name;
        $this->value = $value;
    }

    public function render()
    {
        return view('livewire.backend.richtext-editor-component');
    }
}
