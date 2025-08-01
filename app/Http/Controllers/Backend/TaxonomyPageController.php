<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyPage;
use App\Rules\Slug;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class TaxonomyPageController extends Controller
{

    public function create()
    {
        try {
            $taxonomies = Taxonomy::select('id', 'name')->orderBy('name')->get();
            return view('backend.taxonomy_page.create', compact('taxonomies'));
        } catch (\Throwable $ex) {
            Log::error('Failed to load TaxonomyPage create page', [
                'error' => $ex->getMessage(),
            ]);
            return abort(500);
        }
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'taxonomy_id'  => 'nullable|exists:taxonomies,id',
            'slug'         => [
                'string',
                'max:255',
                'unique:taxonomy_pages',
                new Slug
            ],
            'html'         => 'string',
        ]);

        try {
            $taxonomyPage = new TaxonomyPage([
                'slug'        => $validated['slug'],
                'html'        => $this->sanitizeHtml($validated['html'] ?? ''),
                'taxonomy_id' => $validated['taxonomy_id'] ?? null,
                'metadata'    => [],
            ]);
            $taxonomyPage->created_by = Auth::user()->id;
            $taxonomyPage->save();

            return redirect()
                ->route('dashboard.taxonomy-pages.index')
                ->with('Success', 'Taxonomy Page created successfully');
        } catch (\Throwable $ex) {
            Log::error('Failed to create a TaxonomyPage', [
                'error' => $ex->getMessage(),
            ]);
            return abort(500);
        }
    }

    public function view(TaxonomyPage $taxonomyPage)
    {
        return view('backend.taxonomy_page.preview', compact('taxonomyPage'));
    }

    public function download($slug)
    {
        $taxonomyPage = TaxonomyPage::where('slug', $slug)->first();
        return response($taxonomyPage->html)
            ->header('Content-Type', 'text/html');
    }

    public function edit(TaxonomyPage $taxonomyPage)
    {
        try {
            $taxonomies = Taxonomy::select('id', 'name')->get();
            return view('backend.taxonomy_page.edit', compact('taxonomyPage', 'taxonomies'));
        } catch (\Throwable $ex) {
            Log::error('Failed to load TaxonomyPage edit page', [
                'page_id' => $taxonomyPage->id,
                'error'   => $ex->getMessage(),
            ]);
            return abort(500);
        }
    }

    public function update(Request $request, TaxonomyPage $taxonomyPage)
    {
        $validated = $request->validate([
            'taxonomy_id' => 'nullable|exists:taxonomies,id',
            'slug' => [
                'required',
                'string',
                'max:255',
                new Slug,
                Rule::unique('taxonomy_pages')->ignore($taxonomyPage->slug, 'slug')
            ],
            'html' => 'string',
        ]);

        // Ensure HTML content is sanitized
        $validated['html'] = $this->sanitizeHtml($validated['html'] ?? '');

        try {
            $taxonomyPage->update($validated);
            $taxonomyPage->updated_by = Auth::user()->id;
            $taxonomyPage->save();

            return redirect()
                ->route('dashboard.taxonomy-pages.index')
                ->with('Success', 'Taxonomy Page details updated successfully');
        } catch (\Throwable $ex) {
            Log::error('Failed to update TaxonomyPage', [
                'page_id' => $taxonomyPage->id,
                'error'   => $ex->getMessage(),
            ]);

            return abort(500);
        }
    }

    public function delete(TaxonomyPage $taxonomyPage)
    {
        return view('backend.taxonomy_page.delete', compact('taxonomyPage'));
    }

    public function destroy(TaxonomyPage $taxonomyPage)
    {
        try {
            Storage::disk('public')->delete($taxonomyPage->file_path);
            $taxonomyPage->delete();

            return redirect()
                ->route('dashboard.taxonomy-pages.index')
                ->with('Success', 'Taxonomy Page deleted successfully');
        } catch (\Throwable $ex) {
            Log::error('Failed to delete TaxonomyPage', [
                'page_id' => $taxonomyPage->id,
                'error'   => $ex->getMessage(),
            ]);

            return abort(500);
        }
    }

    private function sanitizeHtml($html)
    {
        // Allow only safe HTML tags
        return strip_tags($html, '<p><a><b><i><strong><em><ul><ol><li><br><hr><h1><h2><h3><h4><h5><h6><span><div><img><blockquote><pre><code><table><thead><tbody><tfoot><tr><th><td>');
    }
}