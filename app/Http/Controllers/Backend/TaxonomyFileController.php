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
            return view('backend.taxonomy_file.create', compact('taxonomies'));
        } catch (\Throwable $ex) {
            Log::error('Failed to load taxonomy-file creation page', [
                'error' => $ex->getMessage(),
            ]);
            return abort(500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'file'         => 'required|file|mimes:pdf,jpg,jpeg,png|max:20480',
            'file_name'    => 'nullable|string|max:255',
            'taxonomy_id'  => 'nullable|exists:taxonomies,id',
            'metadata'     => 'nullable|json',
        ]);

        try {
            $uploaded    = $request->file('file');
            $relativePath = $uploaded->store('taxonomy_files', 'public');

            $taxonomyFile = new TaxonomyFile([
                'file_name'   => $validated['file_name'] ?? $uploaded->getClientOriginalName(),
                'file_path'   => $relativePath,
                'taxonomy_id' => $validated['taxonomy_id'] ?? null,
                'metadata'    => $validated['metadata'] ? json_decode($validated['metadata'], true) : [],
            ]);

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
        return view('backend.taxonomy_file.view', compact('taxonomyFile'));
    }

    public function download(TaxonomyFile $taxonomyFile)
    {
        return Storage::disk('public')->download(
            $taxonomyFile->file_path,
            $taxonomyFile->file_name
        );
    }

    public function edit(TaxonomyFile $taxonomyFile)
    {
        try {
            $taxonomies = Taxonomy::select('id', 'name')->get();

            return view('backend.taxonomy_file.edit', compact('taxonomyFile', 'taxonomies'));
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
        $validated = $request->validate([
            'file'         => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:20480',
            'taxonomy_id'  => 'nullable|exists:taxonomies,id',
            'metadata'     => 'nullable|json',
        ]);

        try {
            if ($request->hasFile('file')) {
                Storage::disk('public')->delete($taxonomyFile->file_path);

                $uploaded = $request->file('file');
                $taxonomyFile->file_name = $uploaded->getClientOriginalName();
                $taxonomyFile->file_path = $uploaded->store('taxonomy_files', 'public');
            }

            $taxonomyFile->taxonomy_id = $validated['taxonomy_id'] ?? null;
            $taxonomyFile->metadata = $validated['metadata']
                ? json_decode($validated['metadata'], true)
                : $taxonomyFile->metadata;

            $taxonomyFile->updated_by = Auth::user()->id;;
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
        return view('backend.taxonomy_file.delete', compact('taxonomyFile'));
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