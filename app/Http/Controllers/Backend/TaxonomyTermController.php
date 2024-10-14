<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm;

class TaxonomyTermController extends Controller
{
    public function index(Taxonomy $taxonomy)
    {
        return view('backend.taxonomy.terms.index', compact('taxonomy'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(Taxonomy $taxonomy)
    {
        try {
            return view('backend.taxonomy.terms.create', compact('taxonomy'));
        } catch (\Exception $ex) {
            Log::error('Failed to load taxonomy terms creation page', ['error' => $ex->getMessage()]);    
            return abort(500);
        }     
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function store(Request $request, Taxonomy $taxonomy)
    {
        try {
            $validatedData = $request->validate([
                'code' => 'required|unique:taxonomy_terms,code',
                'name' => 'required',
                'metadata' => 'nullable|json',
                'parent_id' => 'nullable|exists:taxonomy_terms,id'
            ]);

            $taxonomyTerm = new TaxonomyTerm($validatedData);
            $taxonomyTerm->taxonomy_id = $taxonomy->id;
            $taxonomyTerm->save();

            return redirect()->route('dashboard.taxonomy.terms.index', $taxonomy)
                             ->with('Success', 'Taxonomy term was created successfully!');
        } catch (\Exception $ex) {
            Log::error('Failed to create taxonomy term', ['error' => $ex->getMessage()]);    
            return back()->withInput()->withErrors(['error' => 'Failed to create taxonomy term. Please try again.']);
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Domains\TaxonomyTerm\Models\TaxonomyTerm $taxonomyTerm
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Taxonomy $taxonomy, TaxonomyTerm $term)
    {
        try {
            return view('backend.taxonomy.terms.edit', compact('taxonomy', 'term'));
        } catch (\Exception $ex) {
            Log::error('Failed to load taxonomy term edit page', ['error' => $ex->getMessage()]);    
            return abort(500);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Domains\TaxonomyTerm\Models\TaxonomyTerm $taxonomyTerm
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Taxonomy $taxonomy, TaxonomyTerm $term)
    {
        try {
            $validatedData = $request->validate([
                'code' => 'required|unique:taxonomy_terms,code,' . $term->id,
                'name' => 'required',
                'metadata' => 'nullable|json',
                'parent_id' => 'nullable|exists:taxonomy_terms,id'
            ]);

            $term->update($validatedData);

            return redirect()->route('dashboard.taxonomy.terms.index', $taxonomy)
                             ->with('Success', 'Taxonomy term was updated successfully!');
        } catch (\Exception $ex) {
            Log::error('Failed to update taxonomy term', ['error' => $ex->getMessage()]);    
            return back()->withInput()->withErrors(['error' => 'Failed to update taxonomy term. Please try again.']);
        }
    }
     /**
     * Confirm to delete the specified resource from storage.
     *
     * @param \App\Domains\TaxonomyTerm\Models\TaxonomyTerm $taxonomyTerm
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function delete(Taxonomy $taxonomy, TaxonomyTerm $term)
    {
        return view('backend.taxonomy.terms.delete', compact('taxonomy', 'term'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Domains\TaxonomyTerm\Models\TaxonomyTerm $taxonomyTerm
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function destroy(Taxonomy $taxonomy, TaxonomyTerm $term)
    {
        try {
            $term->delete();
            return redirect()->route('dashboard.taxonomy.terms.index', $taxonomy)->with('Success', 'Taxonomy term was deleted !');
        } catch (\Exception $ex) {
            Log::error('Failed to delete taxonomy term', ['term_id' => $term->id, 'error' => $ex->getMessage()]);
            return back()->withErrors(['error' => 'Failed to delete taxonomy term. Please try again.']);
        }
    }
}