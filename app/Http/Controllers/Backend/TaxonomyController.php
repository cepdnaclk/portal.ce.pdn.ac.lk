<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm;

class TaxonomyController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        try {
            return view('backend.taxonomy.create');
        } catch (\Exception $ex) {
            Log::error('Failed to load taxonomy creation page', ['error' => $ex->getMessage()]);
            return abort(500);
        }
    }

    /**
     * Preview the resource .
     *
     * @param \App\Domains\Taxonomy\Models\Taxonomy $taxonomy
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function view(Taxonomy $taxonomy)
    {
        $taxonomyData = $taxonomy->to_dict();
        return view('backend.taxonomy.view', compact('taxonomyData'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|unique:taxonomies',
            'name' => 'required',
            'description' => 'nullable',
        ]);

        try {
            $taxonomy = new Taxonomy($validatedData);
            $taxonomy->properties = json_decode($request->properties);
            $taxonomy->created_by = Auth::user()->id;
            $taxonomy->save();
            return redirect()->route('dashboard.taxonomy.index')->with('Success', 'Taxonomy created successfully');
        } catch (\Exception $ex) {
            Log::error('Failed to create taxonomy', ['error' => $ex->getMessage()]);
            return abort(500);
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Domains\Taxonomy\Models\Taxonomy $taxonomy
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Taxonomy $taxonomy)
    {
        try {
            return view('backend.taxonomy.edit', [
                'taxonomy' => $taxonomy,
            ]);
        } catch (\Exception $ex) {
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
        $data = $request->validate([
            'code' => 'string|required',
            'name' => 'string|required',
            'description' => 'nullable',
            'properties' => 'string'
        ]);

        try {
            $originalProperties = json_decode($taxonomy->properties);
            $updatedProperties = json_decode($request->properties);
            if ($this->validateProperties($originalProperties, $updatedProperties)) {
                $taxonomy->update($data);
                $taxonomy->properties = $request->properties;
                $taxonomy->updated_by = Auth::user()->id;
                $taxonomy->save();
            }else{
                return redirect()->route('dashboard.taxonomy.index')->withErrors('Can not update the Taxonomy Properties as it already has associated Taxonomy Terms. Please reassign or delete those first.');
            }
            return redirect()->route('dashboard.taxonomy.index')->with('Success', 'Taxonomy updated successfully');
        } catch (\Exception $ex) {
            Log::error('Failed to update taxonomy', ['error' => $ex->getMessage()]);
            return abort(500);
        }
    }
    private function validateProperties(array $original, array $updated): bool
    {
        // Ensure existing items are not modified
        foreach ($original as $index => $originalItem) {
            if (!isset($updated[$index])) {
                return false; // Missing an existing property
            }

            $updatedItem = $updated[$index];
            if (
                $originalItem->code !== $updatedItem->code || 
                $originalItem->name !== $updatedItem->name || 
                $originalItem->data_type !== $updatedItem->data_type
            ) {
                return false; // An existing property was altered
            }
        }

        // Allow additional properties
        return count($updated) >= count($original);
    }
    /**
     * Confirm to delete the specified resource from storage.
     *
     * @param \App\Domains\Taxonomy\Models\Taxonomy $taxonomy
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function delete(Taxonomy $taxonomy)
    {
        $terms = TaxonomyTerm::where('taxonomy_id', $taxonomy->id)->get();
        return view('backend.taxonomy.delete', compact('taxonomy', 'terms'));
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
            $terms = TaxonomyTerm::where('taxonomy_id', $taxonomy->id)->get();
            if ($terms->count() > 0) {
                return redirect()->route('dashboard.taxonomy.index')
                    ->withErrors('Can not delete the Taxonomy as it already has associated Taxonomy Terms. Please reassign or delete those first.');
            }

            $taxonomy->delete();
            return redirect()->route('dashboard.taxonomy.index')->with('Success', 'Taxonomy was deleted !');
        } catch (\Exception $ex) {
            Log::error('Failed to delete taxonomy', ['taxonomy_id' => $taxonomy->id, 'error' => $ex->getMessage()]);
            return abort(500);
        }
    }
}