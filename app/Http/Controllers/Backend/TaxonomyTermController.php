<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
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
    public function create(TaxonomyTerm $taxonomyTerm, Taxonomy $taxonomy)
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
    public function store(Request $request, Taxonomy $taxonomy, TaxonomyTerm $taxonomyTerm)
    {
        try {
            $validatedData = $request->validate([
                'code' => 'required|unique:taxonomy_terms,code',
                'name' => 'required',
                'taxonomy_id' => 'required|exists:taxonomies,id',
                'parent_id' => 'nullable|exists:taxonomy_terms,id',
                'metadata' => 'array',
            ]);

            foreach ($taxonomy->properties as $property) {
                $metadataKey = "metadata.{$property['code']}";


                switch ($property['data_type']) {
                    case 'string':
                        $request->validate([$metadataKey => 'nullable|string']);
                        break;
                    case 'email':
                        $request->validate([$metadataKey => 'nullable|email']);
                        break;
                    case 'integer':
                        $request->validate([$metadataKey => 'nullable|integer']);
                        break;
                    case 'float':
                        $request->validate([$metadataKey => 'nullable|numeric']);
                        break;
                    case 'boolean':
                        $request->validate([$metadataKey => 'nullable|boolean']);
                        break;
                    case 'date':
                        $request->validate([$metadataKey => 'nullable|date']);
                        break;
                    case 'datetime':
                        $request->validate([$metadataKey => 'nullable|date']);
                        break;
                    case 'url':
                        $request->validate([$metadataKey => 'nullable|url']);
                        break;
                    // case 'image':

                    //     if ($request->hasFile("metadata.{$property['code']}")) {
                    //         $imagePath = $this->uploadThumb(null, $request->file("metadata.{$property['code']}"), "taxonomy_terms");
                    //         $value = $imagePath;
                    //     } else {
                    //         $value = null;
                    //     }
                    //     break;
                    case 'file':
                        $request->validate([$metadataKey => 'nullable|exists:taxonomy_files,id']);
                        break;
                }
            }

            $metadataArray = [];

            foreach ($taxonomy->properties as $property) {
                $value = $request->input("metadata.{$property['code']}");

                if ($property['data_type'] === 'boolean') {
                    $value = $request->has("metadata.{$property['code']}") ? true : false;
                }
                $metadataArray[] = [
                    'code' => $property['code'],
                    'value' => $value === '' ? null : $value
                ];
            }

            $taxonomyTerm = new TaxonomyTerm($validatedData);
            $taxonomyTerm->metadata = $metadataArray;
            $taxonomyTerm->created_by = Auth::user()->id;
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
                'parent_id' => 'nullable|exists:taxonomy_terms,id',
                'metadata' => 'array',
            ]);

            foreach ($taxonomy->properties as $property) {
                $metadataKey = "metadata.{$property['code']}";

                switch ($property['data_type']) {
                    case 'string':
                        $request->validate([$metadataKey => 'nullable|string']);
                        break;
                    case 'email':
                        $request->validate([$metadataKey => 'nullable|email']);
                        break;
                    case 'integer':
                        $request->validate([$metadataKey => 'nullable|integer']);
                        break;
                    case 'float':
                        $request->validate([$metadataKey => 'nullable|numeric']);
                        break;
                    case 'boolean':
                        $request->validate([$metadataKey => 'nullable|boolean']);
                        break;
                    case 'date':
                        $request->validate([$metadataKey => 'nullable|date']);
                        break;
                    case 'datetime':
                        $request->validate([$metadataKey => 'nullable|date']);
                        break;
                    case 'url':
                        $request->validate([$metadataKey => 'nullable|url']);
                        break;
                    case 'image':
                        if ($request->hasFile("metadata.{$property['code']}")) {
                            $imagePath = $this->uploadThumb($term, $request->file("metadata.{$property['code']}"), "taxonomy_terms");
                            $value = $imagePath;
                        } else {
                            $value = null;
                        }
                        break;
                    case 'file':
                        $request->validate([$metadataKey => 'nullable|exists:taxonomy_files,id']);
                        break;
                }
            }

            $metadataArray = [];
            foreach ($taxonomy->properties as $property) {
                $value = $request->input("metadata.{$property['code']}");

                if ($property['data_type'] === 'boolean') {
                    $value = $request->has("metadata.{$property['code']}") ? true : false;
                }

                $metadataArray[] = [
                    'code' => $property['code'],
                    'value' => $value === '' ? null : $value
                ];
            }

            $term->update($validatedData);
            $term->metadata = $metadataArray;
            $term->updated_by = Auth::user()->id;
            $term->save();

            return redirect()->route('dashboard.taxonomy.terms.index', $taxonomy)
                ->with('Success', 'Taxonomy term was updated successfully!');
        } catch (\Exception $ex) {
            Log::error('Failed to update taxonomy term', ['term_id' => $term->id, 'error' => $ex->getMessage()]);
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