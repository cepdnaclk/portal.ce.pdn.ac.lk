<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;

class ExpandableInfoCard extends Component
{
    public string $title;
    public string $description;
    public bool $isExpanded = false;

    public function mount(string $title, string $description): void
    {
        $this->title = $title;
        $this->description = $description;
    }

    public function toggleInfo(): void
    {
        $this->isExpanded = !$this->isExpanded;
    }

    public function render()
    {
        return view('livewire.backend.expandable-info-card');
    }
}