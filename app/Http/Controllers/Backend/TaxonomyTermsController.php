<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Domains\TaxonomyTerms\Models\TaxonomyTerms;

class TaxonomyTermsController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        try{
            return view('backend.taxonomyTerms.create');
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
     * @param \App\Domains\TaxonomyTerms\Models\TaxonomyTerms $taxonomyTerms
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(TaxonomyTerms $taxonomyTerms)
    {
        try{
            return view('backend.taxonomyTerms.edit', ['taxonomyTerms' => $taxonomyTerms]);
        }catch (\Exception $ex) {
            Log::error('Failed to load taxonomy terms edit page', ['error' => $ex->getMessage()]);    
            return abort(500);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Domains\TaxonomyTerms\Models\TaxonomyTerms $taxonomyTerms
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, TaxonomyTerms $taxonomyTerms)
    {

    }
     /**
     * Confirm to delete the specified resource from storage.
     *
     * @param \App\Domains\TaxonomyTerms\Models\TaxonomyTerms $taxonomyTerms
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function delete(TaxonomyTerms $taxonomyTerms)
    {
        return view('backend.taxonomyTerms.delete', compact('taxonomyTerms'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Domains\TaxonomyTerms\Models\TaxonomyTerms $taxonomyTerms
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function destroy(TaxonomyTerms $taxonomyTerms)
    {
        try {
            $taxonomyTerms->delete();
            return redirect()->route('dashboard.taxonomyTerms.index')->with('Success', 'Taxonomy term was deleted !');
        } catch (\Exception $ex) {
            Log::error('Failed to delete taxonomy term', ['taxonomyTerms_id' => $taxonomyTerms->id, 'error' => $ex->getMessage()]);
            return abort(500);
        }
    }
}

