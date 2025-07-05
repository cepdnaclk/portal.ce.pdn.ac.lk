<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;

class RichtextEditorComponent extends Component
{
    public $name;
    public $value;
    public $style;

    public function mount($name, $value = '', $style = '')
    {
        $this->name = $name;
        $this->value = $value;
        $this->style = $style;
    }

    public function render()
    {
        return view('livewire.backend.richtext-editor-component');
    }
}
