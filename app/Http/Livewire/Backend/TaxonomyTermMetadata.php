<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;

class TaxonomyTermMetadata extends Component
{
    public $property;
    public $metadata = []; // Holds the pre-filled metadata for edit
    public $term; // Holds the term object for edit
    public $taxonomy_files = [];
    public $taxonomy_pages = [];
    public $taxonomy_terms = [];

    public function mount($property, $term = null, $taxonomy = null)
    {
        $this->property = $property;
        $this->term = $term;

        // Populate metadata if $term is provided (Edit mode)
        if ($this->term) {
            $this->metadata = $this->term->metadata ?? [];
        }
        if ($taxonomy) {
            $this->taxonomy_files = ['' => 'Select a file'];
            foreach ($taxonomy->files()->toArray() as $key => $file_name) {
                $this->taxonomy_files[$key] = $file_name;
            }

            $this->taxonomy_pages = ['' => 'Select a page'];
            foreach ($taxonomy->pages()->toArray() as $key => $page_slug) {
                $this->taxonomy_pages[$key] = $page_slug;
            }

            // Build a list of child taxonomy terms (non-root) for selection
            $this->taxonomy_terms = ['' => 'Select a taxonomy term'];
            foreach ($taxonomy->terms as $t) {
                if (!is_null($t->parent_id)) {
                    $this->taxonomy_terms[$t->id] = \App\Domains\Taxonomy\Models\TaxonomyTerm::getHierarchicalPath($t->id);
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.backend.taxonomy-term-metadata');
    }
}
