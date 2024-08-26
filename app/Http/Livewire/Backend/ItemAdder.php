<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;

class ItemAdder extends Component
{
    public $type;
    public $items = [];
    public $size = "col-6";

    protected $listeners = ['refreshItems' => 'refreshItems'];

    public function mount($type, $items = [])
    {
        $this->type = $type;
        $this->items = $items;
    }

    public function addItem($item)
    {
        $this->items[] = $item;
        $this->emitUp('itemsUpdated', $this->type, $this->items);
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->emitUp('itemsUpdated', $this->type, $this->items);
    }

    public function refreshItems($items)
    {
        $this->items = $items;
    }

    public function render()
    {
        return view('livewire.backend.item-adder');
    }
}