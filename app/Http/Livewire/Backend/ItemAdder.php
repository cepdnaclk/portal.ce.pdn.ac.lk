<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;

class ItemAdder extends Component
{
    public $items = [];
    public $type;
    public $title;

    public function mount($type, $title, $items)
    {
        $this->items = $items;
        $this->type = $type;
        $this->title = $title;
    }

    public function render()
    {
        return view('livewire.backend.item-adder');
    }
}