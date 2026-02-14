<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Jfcherng\Diff\DiffHelper;
use App\Http\Controllers\Controller;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
use App\Domains\Taxonomy\Models\TaxonomyFile;
use App\Domains\Tenant\Services\TenantResolver;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Validation\Rule;

class TaxonomyController extends Controller
{
  public function __construct(private TenantResolver $tenantResolver) {}

  /**
   * Show the form for creating a new resource.
   *
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
   */
  public function create()
  {
    try {
      $tenants = $this->getAvailableTenants();
      $selectedTenantId = $this->getSelectedTenantId($tenants);

      return view('backend.taxonomy.create', compact('tenants', 'selectedTenantId'));
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
    $availableTenantIds = $this->getAvailableTenantIds($request);
    $tenantId = $this->resolveTenantId($request, $this->tenantResolver);
    if ($tenantId && in_array($tenantId, $availableTenantIds, true)) {
      $request->merge(['tenant_id' => $tenantId]);
    }

    $validatedData = $request->validate([
      'code' => 'required|unique:taxonomies',
      'name' => 'required',
      'description' => 'nullable',
      'properties' => 'string',
      'visibility' => 'nullable|integer',
      'tenant_id' => ['required', Rule::in($availableTenantIds)],
    ]);

    try {
      $taxonomy = new Taxonomy($validatedData);
      $taxonomy->properties = json_decode($request->properties);
      $taxonomy->visibility = ($request->visibility !== null);
      $taxonomy->tenant_id = $validatedData['tenant_id'];
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
      $tenants = $this->getAvailableTenants();
      $selectedTenantId = $taxonomy->tenant_id;

      return view('backend.taxonomy.edit', [
        'taxonomy' => $taxonomy,
        'tenants' => $tenants,
        'selectedTenantId' => $selectedTenantId,
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
    $availableTenantIds = $this->getAvailableTenantIds($request);
    $tenantId = $this->resolveTenantId($request, $this->tenantResolver);
    if ($tenantId && in_array($tenantId, $availableTenantIds, true)) {
      $request->merge(['tenant_id' => $tenantId]);
    }

    $data = $request->validate([
      'code' => 'string|required',
      'name' => 'string|required',
      'description' => 'nullable',
      'properties' => 'string',
      'visibility' => 'nullable|integer',
      'tenant_id' => ['required', Rule::in($availableTenantIds)],
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
      $taxonomy->tenant_id = $data['tenant_id'];
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
    foreach ($original as $item) {
      $originalMap[$item->code] = $item;
    }
    foreach ($updated as $item) {
      $updatedMap[$item->code] = $item;
    }

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
      ->paginate(15);

    // Convert activities items to array for processing
    $activitiesData = $activities->items();

    foreach ($activitiesData as &$activity) {
      // Convert model to array
      $activity = $activity->toArray();
      $diffs = [];
      if ($activity['description'] === 'created') {
        // Created
        if ($activity['properties']) {
          foreach ($activity['properties']['attributes'] as $field => $newValue) {
            $newString = is_array($newValue)
              ? json_encode($newValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
              : (string) ($newValue ?? '');

            $diffs[$field] = DiffHelper::calculate(
              '',
              $newString,
              Config::get('diff-helper.renderer', 'Combined'),
              Config::get('diff-helper.calculate_options', []),
              Config::get('diff-helper.render_options', [])
            );
          }
        }
      } elseif ($activity['description'] === 'deleted') {
        // Deleted
        foreach ($activity['properties']['attributes'] as $field => $oldValue) {
          $oldString = is_array($oldValue)
            ? json_encode($oldValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            : (string) ($oldValue ?? '');

          $diffs[$field] = DiffHelper::calculate(
            $oldString,
            '',
            Config::get('diff-helper.renderer', 'Combined'),
            Config::get('diff-helper.calculate_options', []),
            Config::get('diff-helper.render_options', [])
          );
        }
      } elseif (isset($activity['properties']['attributes']) && isset($activity['properties']['old'])) {
        // Updated
        foreach ($activity['properties']['attributes'] as $field => $newValue) {
          $oldValue = $activity['properties']['old'][$field] ?? null;

          $oldString = is_array($oldValue)
            ? json_encode($oldValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            : (string) ($oldValue ?? '');
          $newString = is_array($newValue)
            ? json_encode($newValue, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
            : (string) ($newValue ?? '');


          if ($oldString === $newString) {
            continue;
          }

          $diffs[$field] = DiffHelper::calculate(
            $oldString,
            $newString,
            Config::get('diff-helper.renderer', 'Combined'),
            Config::get('diff-helper.calculate_options', []),
            Config::get('diff-helper.render_options', [])
          );
        }
      }

      $activity['diffs'] = $diffs;
      $activity['created_at'] = Carbon::parse($activity['created_at'])->format('Y-m-d H:i');
    }

    // Replace items in paginator with processed data
    $activities->setCollection(collect($activitiesData));

    return view('backend.taxonomy.history', [
      'taxonomy'   => $taxonomy,
      'activities' => $activities,
      'diffCss'    => DiffHelper::getStyleSheet(),
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

  /**
   * Redirect to a Taxonomy list page using the taxonomy as an alias.
   *
   * @param string $code
   * @return \Illuminate\Http\RedirectResponse
   */
  public function alias($code)
  {
    $taxonomy = Taxonomy::where('code', $code)->firstOrFail();
    $tenantId = $taxonomy->tenant_id;
    if ($tenantId && auth()->user() && ! auth()->user()->hasTenantAccess($tenantId)) {
      abort(403, __('You do not have access to that tenant.'));
    }
    $url = route('dashboard.taxonomy.terms.index', [
      'taxonomy' => $taxonomy,
    ]);

    return redirect($url);
  }

  private function getAvailableTenants()
  {
    return $this->tenantResolver
      ->availableTenantsForUser(auth()->user())
      ->sortBy('name')
      ->values();
  }

  private function getAvailableTenantIds(Request $request): array
  {
    return $this->tenantResolver
      ->availableTenantsForUser($request->user())
      ->pluck('id')
      ->all();
  }

  private function getSelectedTenantId($tenants): ?int
  {
    $defaultTenantId = $this->tenantResolver->resolveDefault()?->id;

    if ($defaultTenantId && $tenants->contains('id', $defaultTenantId)) {
      return (int) $defaultTenantId;
    }

    if ($tenants->count() === 1) {
      return (int) $tenants->first()->id;
    }

    return null;
  }
}