<?php

namespace App\Livewire\Backend;

use Livewire\Component;

class ItemAdder extends Component
{
    public $items = [];
    public $type;

    public function mount($type, $items)
    {
        $this->items = $items;
        $this->type = $type;
    }

    public function render()
    {
        return view('livewire.backend.item-adder');
    }
}
