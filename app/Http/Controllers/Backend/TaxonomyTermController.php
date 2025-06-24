<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
use phpDocumentor\Reflection\PseudoTypes\False_;
use Spatie\Activitylog\Models\Activity;

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
     * @param Request $request
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
     * @param Request $request
     * @param \App\Domains\Taxonomy\Models\TaxonomyTerm $taxonomyTerm
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

            $metadataArray = [];
            foreach ($taxonomy->properties as $property) {
                $value = $request->input("metadata.{$property['code']}");

                if ($property['data_type'] === 'boolean') {
                    $value = $request->has("metadata.{$property['code']}") ? true : false;
                }

                $metadataArray[] =  [
                    'code' => $property['code'],
                    'value' => $value === '' ? null : $value
                ];
            }

            // Exclude 'metadata' from $validatedData before update
            unset($validatedData['metadata']);
            $term->update($validatedData);

            // Update metadata only if it has changed
            if (json_encode($term->metadata) !== json_encode($metadataArray)) {
                $term->metadata = $metadataArray;
            }
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

    /**
     * Display activity log for the given taxonomy term.
     */
    public function history(Taxonomy $taxonomy, TaxonomyTerm $term)
    {
        $activities = Activity::where('subject_type', TaxonomyTerm::class)
            ->where('subject_id', $term->id)
            ->with(['causer', 'subject'])
            ->orderByDesc('created_at')
            ->get();


        // Convert activities collection to array
        $activities = $activities->toArray();


        // TODO implement a better way to visualize the diff
        // Highlight changes for JSON/list attributes in activity log
        foreach ($activities as &$activity) {
            if (isset($activity['properties']['attributes']) && isset($activity['properties']['old'])) {
                // For each activity
                $oldMetadata = array();
                $newMetadata = array();

                if (isset($activity['properties']['old']['metadata'])) {
                    foreach ($activity['properties']['old']['metadata'] as $key => $item) {
                        if (isset($item['code']) && isset($item['value'])) {
                            $oldMetadata[$item['code']] = $item['value'] ?? null;
                        }
                    }
                    ksort($oldMetadata);
                    $activity['properties']['old']['metadata'] = $oldMetadata;
                }

                if (isset($activity['properties']['attributes']['metadata'])) {
                    foreach ($activity['properties']['attributes']['metadata'] as $key => $item) {
                        if (isset($item['code']) && isset($item['value'])) {
                            $newMetadata[$item['code']] = $item['value'] ?? null;
                        }
                    }
                    ksort($newMetadata);
                    $activity['properties']['attributes']['metadata'] = $newMetadata;
                }
            }
        }

        return view('backend.taxonomy.terms.history', [
            'taxonomy'   => $taxonomy,
            'term'       => $term,
            'activities' => $activities,
        ]);
    }
}