<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyFile;
use App\Domains\Tenant\Services\TenantResolver;
use App\Support\Concerns\ResolvesAvailableTenants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TaxonomyFileController extends Controller
{
  use ResolvesAvailableTenants;

  public function __construct(private TenantResolver $tenantResolver) {}

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
      $supportedExtensions = implode(', ', TaxonomyFile::$supportedExtensions);
      return view('backend.taxonomy_file.create', compact('taxonomies', 'supportedExtensions', 'tenants', 'selectedTenantId'));
    } catch (\Throwable $ex) {
      Log::error('Failed to load taxonomy-file creation page', [
        'error' => $ex->getMessage(),
      ]);
      return abort(500);
    }
  }

  public function store(Request $request)
  {
    $availableTenantIds = $this->getAvailableTenantIds($request->user());
    $tenantId = $this->resolveTenantId($request, $availableTenantIds);
    if ($tenantId) {
      $request->merge(['tenant_id' => $tenantId]);
    }

    $supportedExtensions = TaxonomyFile::$supportedExtensions;
    $validated = $request->validate([
      'file'         => 'nullable|file|mimes:' . implode(',', $supportedExtensions) . '|max:10240',
      'file_name'    => 'string|max:255|unique:taxonomy_files,file_name',
      'taxonomy_id'  => [
        'nullable',
        Rule::exists('taxonomies', 'id')
          ->where(fn($query) => $query->where('tenant_id', $request->input('tenant_id'))),
      ],
      'tenant_id' => ['required', Rule::in($availableTenantIds)],
    ]);

    try {
      $uploaded = $request->file('file');

      if ($uploaded) {
        $originalExtension = $uploaded->getClientOriginalExtension();
        $fileNameWithExtension = $validated['file_name'] . '.' . $originalExtension;
        $relativePath = $uploaded->storeAs('taxonomy_files', $fileNameWithExtension, 'public');

        // Update metadata
        $fileSize = $uploaded->getSize();
      } else {
        return redirect()
          ->route('dashboard.taxonomy-files.create')
          ->withErrors(['file' => 'No file was uploaded.']);
      }

      $taxonomyFile = new TaxonomyFile([
        'file_name'   => $validated['file_name'],
        'file_path'   => $relativePath,
        'taxonomy_id' => $validated['taxonomy_id'] ?? null,
        'tenant_id' => $validated['tenant_id'],
      ]);

      $taxonomyFile->metadata = ['file_size' => $fileSize];
      $taxonomyFile->created_by = Auth::user()->id;
      $taxonomyFile->save();

      return redirect()
        ->route('dashboard.taxonomy-files.index')
        ->with('Success', 'Taxonomy File uploaded successfully');
    } catch (\Throwable $ex) {
      Log::error('Failed to upload taxonomy file', [
        'error' => $ex->getMessage(),
      ]);

      return abort(500);
    }
  }

  public function view(TaxonomyFile $taxonomyFile)
  {
    $showPreview = in_array($taxonomyFile->getFileExtension(), TaxonomyFile::$supportedImageTypes);
    return view('backend.taxonomy_file.view', compact('taxonomyFile', 'showPreview'));
  }

  public function download($fileNameWithExtension)
  {
    // Remove extension from file name for lookup
    $fileName = pathinfo($fileNameWithExtension, PATHINFO_FILENAME);

    $taxonomyFile = TaxonomyFile::where('file_name', $fileName)->first();

    if (!$taxonomyFile) {
      return abort(404, 'File not found.');
    }

    if (!Storage::disk('public')->exists($taxonomyFile->file_path)) {
      return abort(404, 'File not found.');
    }

    return Storage::disk('public')->download($taxonomyFile->file_path);
  }


  public function edit(TaxonomyFile $taxonomyFile)
  {
    try {
      $tenants = $this->getAvailableTenants();
      $selectedTenantId = $taxonomyFile->tenant_id;
      $taxonomies = Taxonomy::query()
        ->select('id', 'name', 'tenant_id')
        ->whereIn('tenant_id', $this->getAvailableTenantIds(auth()->user()))
        ->get();
      $supportedExtensions = implode(', ', TaxonomyFile::$supportedExtensions);

      return view('backend.taxonomy_file.edit', compact('taxonomyFile', 'taxonomies', 'supportedExtensions', 'tenants', 'selectedTenantId'));
    } catch (\Throwable $ex) {
      Log::error('Failed to load taxonomy-file edit page', [
        'file_id' => $taxonomyFile->id,
        'error'   => $ex->getMessage(),
      ]);

      return abort(500);
    }
  }

  public function update(Request $request, TaxonomyFile $taxonomyFile)
  {
    $availableTenantIds = $this->getAvailableTenantIds($request->user());
    $tenantId = $this->resolveTenantId($request, $availableTenantIds);
    if ($tenantId) {
      $request->merge(['tenant_id' => $tenantId]);
    }

    $supportedExtensions = TaxonomyFile::$supportedExtensions;
    $validated = $request->validate([
      'file'         => 'nullable|file|mimes:' . implode(',', $supportedExtensions) . '|max:10240',
      'file_name'    => 'required|string|max:255|unique:taxonomy_files,file_name,' . $taxonomyFile->id,
      'taxonomy_id'  => [
        'nullable',
        Rule::exists('taxonomies', 'id')
          ->where(fn($query) => $query->where('tenant_id', $request->input('tenant_id'))),
      ],
      'tenant_id' => ['required', Rule::in($availableTenantIds)],
    ]);

    try {
      $taxonomyFile->taxonomy_id = $validated['taxonomy_id'] ?? null;
      $taxonomyFile->tenant_id = $validated['tenant_id'];

      if ($request->hasFile('file')) {
        Storage::disk('public')->delete($taxonomyFile->file_path);

        $uploaded = $request->file('file');
        $taxonomyFile->file_name = $validated['file_name'];

        // Save new file in the storage
        $originalExtension = $uploaded->getClientOriginalExtension();
        $fileNameWithExtension = $validated['file_name'] . '.' . $originalExtension;
        $taxonomyFile->file_path = $uploaded->storeAs('taxonomy_files', $fileNameWithExtension, 'public');

        // Update metadata
        $fileSize = $uploaded->getSize();
        $metadata = $taxonomyFile->metadata ?? [];
        $metadata['file_size'] = $fileSize;
        $taxonomyFile->metadata = $metadata;
      } else {
        $taxonomyFile->file_name = $validated['file_name'];
      }

      $taxonomyFile->updated_by = Auth::user()->id;
      $taxonomyFile->save();

      return redirect()
        ->route('dashboard.taxonomy-files.index')
        ->with('Success', 'Taxonomy File details updated successfully');
    } catch (\Throwable $ex) {
      Log::error('Failed to update taxonomy file', [
        'file_id' => $taxonomyFile->id,
        'error'   => $ex->getMessage(),
      ]);

      return abort(500);
    }
  }

  public function delete(TaxonomyFile $taxonomyFile)
  {
    $showPreview = in_array($taxonomyFile->getFileExtension(), TaxonomyFile::$supportedImageTypes);
    return view('backend.taxonomy_file.delete', compact('taxonomyFile', 'showPreview'));
  }

  public function destroy(TaxonomyFile $taxonomyFile)
  {
    try {
      Storage::disk('public')->delete($taxonomyFile->file_path);
      $taxonomyFile->delete();

      return redirect()
        ->route('dashboard.taxonomy-files.index')
        ->with('Success', 'Taxonomy File deleted successfully');
    } catch (\Throwable $ex) {
      Log::error('Failed to delete taxonomy file', [
        'file_id' => $taxonomyFile->id,
        'error'   => $ex->getMessage(),
      ]);

      return abort(500);
    }
  }
}