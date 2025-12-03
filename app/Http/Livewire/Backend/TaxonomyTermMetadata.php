<?php

namespace App\Http\Livewire\Backend;

use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyList;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;

class TaxonomyTermMetadata extends Component
{
    public $property;
    public $metadata = []; // Holds the pre-filled metadata for edit
    public $term; // Holds the term object for edit
    public $taxonomy_files = [];
    public $taxonomy_pages = [];
    public $taxonomy_terms = [];
    public $taxonomy_lists = [];

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

            $this->taxonomy_lists = ['' => 'Select a list'];
            foreach ($taxonomy->lists()->toArray() as $key => $listName) {
                $this->taxonomy_lists[$key] = $listName;
            }
            // Load Non-related Taxonomy Lists
            // foreach (TaxonomyList::whereNull('taxonomy_id')->orderBy('name')->get(['id', 'name']) as $globalList) {
            //     $this->taxonomy_lists[$globalList->id] = $globalList->name;
            // }

            // Build a list of child taxonomy terms (non-root) for selection
            $this->taxonomy_terms = Cache::remember('taxonomy_terms_hierarchical_list_' . ($this->term->id ?? 'all'), 300, function () {
              $list = ['' => 'Select a taxonomy term'];
              foreach (Taxonomy::with('terms')->get() as $tx) {
                  foreach ($tx->terms as $t) {
                      if (!$this->term || $t->code != $this->term->code) {
                          // Skip the current term to avoid circular dependency
                          $list[$t->id] = \App\Domains\Taxonomy\Models\TaxonomyTerm::getHierarchicalPath($t->id);
                      }
                  }
              }
              return $list;
            });
        }
    }

    public function render()
    {
        return view('livewire.backend.taxonomy-term-metadata');
    }
}