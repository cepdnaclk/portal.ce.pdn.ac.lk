<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Domains\Taxonomy\Models\Taxonomy;

class TaxonomyController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        try{
            return view('backend.taxonomy.create');
        }catch (\Exception $ex) {
            Log::error('Failed to load taxonomy creation page', ['error' => $ex->getMessage()]);    
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
     * @param \App\Domains\Taxonomy\Models\Taxonomy $taxonomy
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Taxonomy $taxonomy)
    {
        try{
            return view('backend.taxonomy.edit', ['taxonomy' => $taxonomy]);
        }catch (\Exception $ex) {
            Log::error('Failed to load taxonomy edit page', ['error' => $ex->getMessage()]);    
            return abort(500);
        }
    }
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Domains\Taxonomy\Models\Taxonomy $taxonomy
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Taxonomy $taxonomy)
    {

    }
     /**
     * Confirm to delete the specified resource from storage.
     *
     * @param \App\Domains\Taxonomy\Models\Taxonomy $taxonomy
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function delete(Taxonomy $taxonomy)
    {
        return view('backend.taxonomy.delete', compact('taxonomy'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Domains\Taxonomy\Models\Taxonomy $taxonomy
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function destroy(Taxonomy $taxonomy)
    {
        try {
            $taxonomy->delete();
            return redirect()->route('dashboard.taxonomy.index')->with('Success', 'Taxonomy was deleted !');
        } catch (\Exception $ex) {
            Log::error('Failed to delete taxonomy', ['taxonomy_id' => $taxonomy->id, 'error' => $ex->getMessage()]);
            return abort(500);
        }
    }
}

