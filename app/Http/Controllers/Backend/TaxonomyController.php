<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
use App\Domains\Taxonomy\Models\TaxonomyFile;
use Spatie\Activitylog\Models\Activity;

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
            'properties' => 'string',
            'visibility' => 'nullable|integer'
        ]);

        try {
            $taxonomy = new Taxonomy($validatedData);
            $taxonomy->properties = json_decode($request->properties);
            $taxonomy->visibility = ($request->visibility !== null);
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
            'properties' => 'string',
            'visibility' => 'nullable|integer'
        ]);
        try {
            $originalProperties = $taxonomy->properties;
            $updatedProperties = json_decode($request->properties);

            // TODO Come up with a better approach
            // if ($taxonomy->terms->count() > 0 && !$this->validateProperties($originalProperties, $updatedProperties)) {
            //     return redirect()
            //         ->route('dashboard.taxonomy.index')
            //         ->withErrors('Can not update the Taxonomy Properties as it already has associated Taxonomy Terms. Please reassign or delete those first.');
            // }

            // Exclude 'properties' from $data before update
            $updateData = $data;
            unset($updateData['properties']);
            $taxonomy->update($updateData);

            if (json_encode($originalProperties) !== $request->properties) {
                $taxonomy->properties = $updatedProperties;
            }
            $newVisibility = ($request->visibility !== null) ? true : false;
            if ($taxonomy->visibility !== $newVisibility) {
                $taxonomy->visibility = $newVisibility;
            }
            $taxonomy->updated_by = Auth::user()->id;
            $taxonomy->save();
            return redirect()->route('dashboard.taxonomy.index')->with('Success', 'Taxonomy updated successfully');
        } catch (\Exception $ex) {
            Log::error('Failed to update taxonomy', ['error' => $ex->getMessage()]);
            return abort(500);
        }
    }

    // TODO use different approach for this
    private function validateProperties(array $original, array $updated): bool
    {
        $originalMap = [];
        $updatedMap = [];
        foreach ($original as $item)  $originalMap[$item->code] = $item;
        foreach ($updated as $item) $updatedMap[$item->code] = $item;

        // Ensure existing items are not modified
        foreach ($originalMap as $code => $originalItem) {
            if (!isset($updatedMap[$code])) {
                // TODO Let allow to delete if not used in any term
                return false; // Missing an existing property
            }

            $updatedItem = $updatedMap[$code];
            if (
                $originalItem->data_type !== $updatedItem->data_type
            ) {
                // An existing property data type was altered
                // TODO Let allow to delete if not used in any term
                return false;
            }
        }
        return true;
    }

    /**
     * Display activity log for the given taxonomy.
     */
    public function history(Taxonomy $taxonomy)
    {
        $termIds = TaxonomyTerm::where('taxonomy_id', $taxonomy->id)->pluck('id');
        $fileIds = TaxonomyFile::where('taxonomy_id', $taxonomy->id)->pluck('id');

        $activities = Activity::where(function ($query) use ($taxonomy, $termIds, $fileIds) {
            $query->where(function ($q) use ($taxonomy) {
                $q->where('subject_type', Taxonomy::class)
                    ->where('subject_id', $taxonomy->id);
            })
                ->orWhere(function ($q) use ($termIds) {
                    $q->where('subject_type', TaxonomyTerm::class)
                        ->whereIn('subject_id', $termIds);
                })
                ->orWhere(function ($q) use ($fileIds) {
                    $q->where('subject_type', TaxonomyFile::class)
                        ->whereIn('subject_id', $fileIds);
                });
        })
            ->with(['causer', 'subject'])
            ->orderByDesc('created_at')
            ->get();

        // Convert activities collection to array
        $activities = $activities->toArray();

        // TODO implement a better way to visualize the diff
        // Highlight changes for JSON/list attributes in activity log
        foreach ($activities as &$activity) {
            if (isset($activity['properties']['attributes']) && isset($activity['properties']['old'])) {
                // For each activity,
                foreach ($activity['properties']['attributes'] as $key => $newValue) {
                    //  For each attribute
                    $oldValue = $activity['properties']['old'][$key] ?? null;

                    // If value is an array, compare and keep only show only the diff
                    if (is_array($newValue) && is_array($oldValue)) {
                        if (json_encode($newValue) == json_encode($oldValue)) {
                            $activity['properties']['old'][$key] = "...";
                            $activity['properties']['attributes'][$key] = "...";
                        } else {
                            // Iterate through each key in the new value
                            foreach ($newValue as $subKey => $subValue) {
                                // If the sub-value is different from the old value, highlight it
                                if (array_key_exists($subKey, $oldValue)) {
                                    if (json_encode($oldValue[$subKey]) == json_encode($subValue)) {
                                        $activity['properties']['old'][$key][$subKey] = "...";
                                        $activity['properties']['attributes'][$key][$subKey] = "...";
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        return view('backend.taxonomy.history', [
            'taxonomy'   => $taxonomy,
            'activities' => $activities,
        ]);
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
