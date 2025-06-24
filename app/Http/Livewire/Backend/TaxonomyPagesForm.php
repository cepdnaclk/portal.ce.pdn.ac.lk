<?php

namespace App\Http\Livewire\Backend;

use Livewire\Component;
use App\Domains\Taxonomy\Models\TaxonomyPage;
use App\Domains\Taxonomy\Models\Taxonomy;
use Illuminate\Support\Facades\Auth;

class TaxonomyPagesForm extends Component
{
    public ?TaxonomyPage $page = null;
    public $slug = '';
    public $html = '';
    public $taxonomy_id = null;

    /** @return array */
    protected function rules()
    {
        $id = $this->page->id ?? null;
        return [
            'slug' => ['required', 'string', 'max:255', 'unique:taxonomy_pages,slug,' . $id],
            'html' => ['required', 'string'],
            'taxonomy_id' => ['nullable', 'exists:taxonomies,id'],
        ];
    }

    public function mount(?TaxonomyPage $taxonomyPage = null): void
    {
        if ($taxonomyPage) {
            $this->page = $taxonomyPage;
            $this->slug = $taxonomyPage->slug;
            $this->html = $taxonomyPage->html;
            $this->taxonomy_id = $taxonomyPage->taxonomy_id;
        }
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'slug' => $this->slug,
            'html' => $this->html,
            'taxonomy_id' => $this->taxonomy_id,
        ];

        if ($this->page) {
            $data['updated_by'] = Auth::id();
            $this->page->update($data);
        } else {
            $data['created_by'] = Auth::id();
            $this->page = TaxonomyPage::create($data);
        }

        session()->flash('Success', 'Taxonomy Page saved successfully');
        redirect()->route('dashboard.taxonomy-pages.index');
    }

    public function render()
    {
        $taxonomies = Taxonomy::select('id', 'name')->orderBy('name')->get();
        return view('livewire.backend.taxonomy-pages-form', [
            'taxonomies' => $taxonomies,
        ]);
    }
}
