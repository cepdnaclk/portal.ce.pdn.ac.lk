<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;

class TaxonomyTermMetadata extends Component
{
    public $property;
    public $metadata = []; // Holds the pre-filled metadata for edit
    public $term; // Holds the term object for edit
    public $taxonomy_files = [];

    public function mount($property, $term = null, $taxonomy = null)
    {
        $this->property = $property;
        $this->term = $term;

        // Populate metadata if $term is provided (Edit mode)
        if ($this->term) {
            $this->metadata = $this->term->metadata ?? [];
        }
        if ($taxonomy) {
            $this->taxonomy_files = [
                ['id' => '11', 'file_name' => 'AA'],
                ['id' => '12', 'file_name' => 'BB']
            ];
        }
    }

    public function render()
    {
        return view('livewire.backend.taxonomy-term-metadata');
    }
}