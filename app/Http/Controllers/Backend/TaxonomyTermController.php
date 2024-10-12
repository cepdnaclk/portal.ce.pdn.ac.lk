<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Domains\Taxonomy\Models\TaxonomyTerm;

class TaxonomyTermController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        try{
            return view('backend.taxonomy.terms.create');
        }catch (\Exception $ex) {
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
    public function store(Request $request)
    {

    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Domains\TaxonomyTerm\Models\TaxonomyTerm $taxonomyTerm
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(TaxonomyTerm $taxonomyTerm)
    {
        try{
            return view('backend.taxonomy.terms.edit', ['taxonomyTerm' => $taxonomyTerm]);
        }catch (\Exception $ex) {
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
    public function update(Request $request, TaxonomyTerm $taxonomyTerm)
    {

    }
     /**
     * Confirm to delete the specified resource from storage.
     *
     * @param \App\Domains\TaxonomyTerm\Models\TaxonomyTerm $taxonomyTerm
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function delete(TaxonomyTerm $taxonomyTerm)
    {
        return view('backend.taxonomy.terms.delete', compact('taxonomyTerm'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Domains\TaxonomyTerm\Models\TaxonomyTerm $taxonomyTerm
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function destroy(TaxonomyTerm $taxonomyTerm)
    {
        try {
            $taxonomyTerm->delete();
            return redirect()->route('dashboard.taxonomy.terms.index')->with('Success', 'Taxonomy term was deleted !');
        } catch (\Exception $ex) {
            Log::error('Failed to delete taxonomy term', ['taxonomyTerm_id' => $taxonomyTerm->id, 'error' => $ex->getMessage()]);
            return abort(500);
        }
    }
}