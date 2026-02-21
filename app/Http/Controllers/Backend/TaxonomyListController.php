<?php

namespace App\Http\Controllers\Backend;

use App\Domains\ContentManagement\Models\Article;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyFile;
use App\Domains\Taxonomy\Models\TaxonomyList;
use App\Domains\Taxonomy\Models\TaxonomyPage;
use App\Domains\Tenant\Services\TenantResolver;
use App\Http\Controllers\Controller;
use App\Support\Concerns\ResolvesAvailableTenants;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Jfcherng\Diff\DiffHelper;
use Spatie\Activitylog\Models\Activity;

class TaxonomyListController extends Controller
{
  use ResolvesAvailableTenants;

  public function __construct(private TenantResolver $tenantResolver) {}

  public function index()
  {
    return view('backend.taxonomy_list.index');
  }

  public function create()
  {
    try {
      $tenants = $this->getAvailableTenants();
      $selectedTenantId = $this->getSelectedTenantId($tenants);
      $taxonomies = Taxonomy::query()
        ->select('id', 'name', 'tenant_id')
        ->whereIn('tenant_id', $this->getAvailableTenantIds(auth()->user()))
        ->orderBy('name')
        ->get();

      return view('backend.taxonomy_list.create', [
        'taxonomies' => $taxonomies,
        'dataTypes' => TaxonomyList::DATA_TYPE_LABELS,
        'tenants' => $tenants,
        'selectedTenantId' => $selectedTenantId,
      ]);
    } catch (\Throwable $ex) {
      Log::error('Failed to load taxonomy list creation page', ['error' => $ex->getMessage()]);
      return abort(500);
    }
  }

  public function store(Request $request)
  {
    $availableTenantIds = $this->getAvailableTenantIds($request->user());
    $tenantId = $this->resolveTenantId($request, $this->tenantResolver);
    if ($tenantId && in_array($tenantId, $availableTenantIds, true)) {
      $request->merge(['tenant_id' => $tenantId]);
    }

    $validated = $request->validate([
      'name' => 'required|string|max:255|unique:taxonomy_lists,name',
      'taxonomy_id' => [
        'nullable',
        Rule::exists('taxonomies', 'id')
          ->where(fn($query) => $query->where('tenant_id', $request->input('tenant_id'))),
      ],
      'data_type' => ['required', 'string', 'max:16', Rule::in(TaxonomyList::DATA_TYPES)],
      'tenant_id' => ['required', Rule::in($availableTenantIds)],
    ]);

    try {
      $taxonomyList = new TaxonomyList($validated);
      $taxonomyList->items = [];
      $taxonomyList->tenant_id = $validated['tenant_id'];
      $taxonomyList->created_by = Auth::user()->id;
      $taxonomyList->save();

      return redirect()
        ->route('dashboard.taxonomy-lists.manage', $taxonomyList)
        ->with('Success', 'Taxonomy List created successfully. You can now add items.');
    } catch (\Throwable $ex) {
      Log::error('Failed to create taxonomy list', ['error' => $ex->getMessage()]);
      return abort(500);
    }
  }

  public function view(TaxonomyList $taxonomyList)
  {
    $taxonomyList->load('taxonomy');

    $fileMap = collect();
    $pageMap = collect();
    $articleMap = collect();

    if ($taxonomyList->data_type === 'file' && is_array($taxonomyList->items)) {
      $fileMap = TaxonomyFile::whereIn('id', $taxonomyList->items)->get()->keyBy('id');
    } elseif ($taxonomyList->data_type === 'page' && is_array($taxonomyList->items)) {
      $pageMap = TaxonomyPage::whereIn('id', $taxonomyList->items)->get()->keyBy('id');
    } elseif ($taxonomyList->data_type === 'article' && is_array($taxonomyList->items)) {
      $articleMap = Article::whereIn('id', $taxonomyList->items)->get()->keyBy('id');
    }

    return view('backend.taxonomy_list.view', [
      'taxonomyList' => $taxonomyList,
      'fileMap' => $fileMap,
      'pageMap' => $pageMap,
      'articleMap' => $articleMap,
    ]);
  }

  public function edit(TaxonomyList $taxonomyList)
  {
    try {
      $taxonomyList->load('taxonomy');
      $tenants = $this->getAvailableTenants();
      $selectedTenantId = $taxonomyList->tenant_id;
      $taxonomies = Taxonomy::query()
        ->select('id', 'name', 'tenant_id')
        ->whereIn('tenant_id', $this->getAvailableTenantIds(auth()->user()))
        ->orderBy('name')
        ->get();

      return view('backend.taxonomy_list.edit', [
        'taxonomyList' => $taxonomyList,
        'taxonomies' => $taxonomies,
        'dataTypes' => TaxonomyList::DATA_TYPE_LABELS,
        'tenants' => $tenants,
        'selectedTenantId' => $selectedTenantId,
      ]);
    } catch (\Throwable $ex) {
      Log::error('Failed to load taxonomy list edit page', [
        'taxonomy_list_id' => $taxonomyList->id,
        'error' => $ex->getMessage(),
      ]);
      return abort(500);
    }
  }

  public function manage(TaxonomyList $taxonomyList)
  {
    try {
      $taxonomyList->load('taxonomy');
      $taxonomyId = $taxonomyList->taxonomy ? $taxonomyList->taxonomy->id : null;
      $tenantId = $taxonomyList->tenant_id;

      if ($taxonomyId) {
        // Load only the resources linked to the selected taxonomy
        $files = TaxonomyFile::where('taxonomy_id', $taxonomyId)
          ->where('tenant_id', $tenantId)
          ->orderBy('file_name')->get(['id', 'file_name']);
        $pages = TaxonomyPage::where('taxonomy_id', $taxonomyId)
          ->where('tenant_id', $tenantId)
          ->orderBy('slug')->get(['id', 'slug']);
      } else {
        // Load all the resources
        $files = TaxonomyFile::where('tenant_id', $tenantId)->orderBy('file_name')->get(['id', 'file_name']);
        $pages = TaxonomyPage::where('tenant_id', $tenantId)->orderBy('slug')->get(['id', 'slug']);
      }

      $articles = Article::orderBy('title')->get(['id', 'title']);

      return view('backend.taxonomy_list.manage', [
        'taxonomyList' => $taxonomyList,
        'files' => $files,
        'pages' => $pages,
        'articles' => $articles,
        'dataTypes' => TaxonomyList::DATA_TYPE_LABELS,
      ]);
    } catch (\Throwable $ex) {
      Log::error('Failed to load taxonomy list manage page', [
        'taxonomy_list_id' => $taxonomyList->id,
        'error' => $ex->getMessage(),
      ]);
      return abort(500);
    }
  }

  public function update(Request $request, TaxonomyList $taxonomyList)
  {
    $availableTenantIds = $this->getAvailableTenantIds($request->user());
    $tenantId = $this->resolveTenantId($request, $this->tenantResolver);
    if ($tenantId && in_array($tenantId, $availableTenantIds, true)) {
      $request->merge(['tenant_id' => $tenantId]);
    }

    $validated = $request->validate([
      'name' => ['required', 'string', 'max:255', Rule::unique('taxonomy_lists', 'name')->ignore($taxonomyList->id)],
      'taxonomy_id' => [
        'nullable',
        Rule::exists('taxonomies', 'id')
          ->where(fn($query) => $query->where('tenant_id', $request->input('tenant_id'))),
      ],
      'data_type' => ['required', 'string', 'max:16', Rule::in(TaxonomyList::DATA_TYPES)],
      'tenant_id' => ['required', Rule::in($availableTenantIds)],
    ]);

    if ($taxonomyList->items && $taxonomyList->data_type !== $validated['data_type']) {
      return back()
        ->withInput()
        ->withErrors(['data_type' => 'Data type cannot be changed while the list contains items.']);
    }

    try {
      $taxonomyList->fill([
        'name' => $validated['name'],
        'taxonomy_id' => $validated['taxonomy_id'] ?? null,
      ]);

      $taxonomyList->data_type = $validated['data_type'];
      $taxonomyList->tenant_id = $validated['tenant_id'];
      $taxonomyList->updated_by = Auth::user()->id;
      $taxonomyList->save();

      return redirect()
        ->route('dashboard.taxonomy-lists.index')
        ->with('Success', 'Taxonomy List updated successfully.');
    } catch (\Throwable $ex) {
      Log::error('Failed to update taxonomy list', [
        'taxonomy_list_id' => $taxonomyList->id,
        'error' => $ex->getMessage(),
      ]);
      return abort(500);
    }
  }

  public function update_list(Request $request, TaxonomyList $taxonomyList)
  {
    $validated = $request->validate([
      'items' => 'nullable|string',
    ]);
    $data_type = $taxonomyList->data_type;
    $items = $this->validateItems($this->decodeItems($validated['items'] ?? '[]'), $data_type, $taxonomyList->tenant_id);

    try {
      $taxonomyList->items = $items;
      $taxonomyList->updated_by = Auth::user()->id;
      $taxonomyList->save();

      return redirect()
        ->route('dashboard.taxonomy-lists.index')
        ->with('Success', 'Taxonomy List updated successfully.');
    } catch (\Throwable $ex) {
      Log::error('Failed to update taxonomy list', [
        'taxonomy_list_id' => $taxonomyList->id,
        'error' => $ex->getMessage(),
      ]);
      return abort(500);
    }
  }

  public function delete(TaxonomyList $taxonomyList)
  {
    return view('backend.taxonomy_list.delete', ['taxonomyList' => $taxonomyList]);
  }

  public function destroy(TaxonomyList $taxonomyList)
  {
    try {
      $taxonomyList->delete();

      return redirect()
        ->route('dashboard.taxonomy-lists.index')
        ->with('Success', 'Taxonomy List deleted successfully');
    } catch (\Throwable $ex) {
      Log::error('Failed to delete taxonomy list', [
        'taxonomy_list_id' => $taxonomyList->id,
        'error' => $ex->getMessage(),
      ]);
      return abort(500);
    }
  }

  public function history(TaxonomyList $taxonomyList)
  {
    $activities = Activity::where('subject_type', TaxonomyList::class)
      ->where('subject_id', $taxonomyList->id)
      ->with(['causer', 'subject'])
      ->orderByDesc('created_at')
      ->get()
      ->toArray();

    foreach ($activities as &$activity) {
      $diffs = [];
      $properties = $activity['properties'] ?? [];
      $attributes = $properties['attributes'] ?? [];
      $oldValues = $properties['old'] ?? [];

      if ($activity['description'] === 'created') {
        foreach ($attributes as $field => $newValue) {
          $newString = $this->normalizeForDiff($newValue);
          $diffs[$field] = DiffHelper::calculate(
            '',
            $newString,
            Config::get('diff-helper.renderer', 'Combined'),
            Config::get('diff-helper.calculate_options', []),
            Config::get('diff-helper.render_options', [])
          );
        }
      } elseif ($activity['description'] === 'deleted') {
        foreach ($attributes as $field => $oldValue) {
          $oldString = $this->normalizeForDiff($oldValue);
          $diffs[$field] = DiffHelper::calculate(
            $oldString,
            '',
            Config::get('diff-helper.renderer', 'Combined'),
            Config::get('diff-helper.calculate_options', []),
            Config::get('diff-helper.render_options', [])
          );
        }
      } else {
        foreach ($attributes as $field => $newValue) {
          $oldValue = $oldValues[$field] ?? null;
          $oldString = $this->normalizeForDiff($oldValue);
          $newString = $this->normalizeForDiff($newValue);

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

    return view('backend.taxonomy_list.history', [
      'taxonomyList' => $taxonomyList,
      'activities' => $activities,
      'diffCss' => DiffHelper::getStyleSheet(),
    ]);
  }

  private function validateItems(array $items, string $dataType, ?int $tenantId): array
  {
    foreach ($items as $index => $value) {
      switch ($dataType) {
        case 'string':
          if (!is_string($value) || trim($value) === '') {
            throw ValidationException::withMessages([
              'items' => "Item #" . ($index + 1) . ' must be a non-empty string.',
            ]);
          }
          $items[$index] = trim($value);
          break;
        case 'date':
          if (!is_string($value) || strtotime($value) === false) {
            throw ValidationException::withMessages([
              'items' => "Item #" . ($index + 1) . ' must be a valid date.',
            ]);
          }
          $items[$index] = date('Y-m-d', strtotime($value));
          break;
        case 'url':
          if (!filter_var($value, FILTER_VALIDATE_URL)) {
            throw ValidationException::withMessages([
              'items' => "Item #" . ($index + 1) . ' must be a valid URL.',
            ]);
          }
          $items[$index] = trim($value);
          break;
        case 'email':
          if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw ValidationException::withMessages([
              'items' => "Item #" . ($index + 1) . ' must be a valid email.',
            ]);
          }
          $items[$index] = strtolower(trim($value));
          break;
        case 'file':
          if (!TaxonomyFile::where('tenant_id', $tenantId)->whereKey($value)->exists()) {
            throw ValidationException::withMessages([
              'items' => "Item #" . ($index + 1) . ' must reference an existing file.',
            ]);
          }
          $items[$index] = (int) $value;
          break;
        case 'page':
          if (!TaxonomyPage::where('tenant_id', $tenantId)->whereKey($value)->exists()) {
            throw ValidationException::withMessages([
              'items' => "Item #" . ($index + 1) . ' must reference an existing page.',
            ]);
          }
          $items[$index] = (int) $value;
          break;
      }
    }

    return $items;
  }

  private function decodeItems(?string $json): array
  {
    if (!$json) {
      return [];
    }

    $decoded = json_decode($json, true);

    if (!is_array($decoded)) {
      throw \Illuminate\Validation\ValidationException::withMessages([
        'items' => 'Items payload is invalid.',
      ]);
    }

    return array_values($decoded);
  }

  private function normalizeForDiff($value): string
  {
    return is_array($value)
      ? json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
      : (string)($value ?? '');
  }
}