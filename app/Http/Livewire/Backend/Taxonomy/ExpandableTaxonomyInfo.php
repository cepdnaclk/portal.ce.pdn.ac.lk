<?php

namespace App\Http\Livewire\Backend\Taxonomy;

use App\Domains\Taxonomy\Models\Taxonomy;
use Livewire\Component;

class ExpandableTaxonomyInfo extends Component
{
    public Taxonomy $taxonomy;
    public bool $isExpanded = false;

    public function mount(Taxonomy $taxonomy): void
    {
        $this->taxonomy = $taxonomy;
    }

    public function toggleInfo(): void
    {
        $this->isExpanded = !$this->isExpanded;
    }

    public function render()
    {
        return view('livewire.backend.taxonomy.expandable-taxonomy-info');
    }
}
