<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TaxonomyFileController extends Controller
{
    public function create()
    {
        try {
            $taxonomies = Taxonomy::select('id', 'name')->orderBy('name')->get();
            $supportedExtensions = implode(', ', TaxonomyFile::$supportedExtensions);
            return view('backend.taxonomy_file.create', compact('taxonomies', 'supportedExtensions'));
        } catch (\Throwable $ex) {
            Log::error('Failed to load taxonomy-file creation page', [
                'error' => $ex->getMessage(),
            ]);
            return abort(500);
        }
    }

    public function store(Request $request)
    {
        $supportedExtensions = TaxonomyFile::$supportedExtensions;
        $validated = $request->validate([
            'file'         => 'nullable|file|mimes:' . implode(',', $supportedExtensions) . '|max:10240',
            'file_name'    => 'string|max:255|unique:taxonomy_files,file_name',
            'taxonomy_id'  => 'nullable|exists:taxonomies,id',
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
            $taxonomies = Taxonomy::select('id', 'name')->get();
            $supportedExtensions = implode(', ', TaxonomyFile::$supportedExtensions);

            return view('backend.taxonomy_file.edit', compact('taxonomyFile', 'taxonomies', 'supportedExtensions'));
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
        $supportedExtensions = TaxonomyFile::$supportedExtensions;
        $validated = $request->validate([
            'file'         => 'nullable|file|mimes:' . implode(',', $supportedExtensions) . '|max:10240',
            'file_name'    => 'required|string|max:255|unique:taxonomy_files,file_name,' . $taxonomyFile->id,
            'taxonomy_id'  => 'nullable|exists:taxonomies,id',
        ]);

        try {
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
