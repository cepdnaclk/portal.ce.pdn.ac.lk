<?php

namespace App\Http\Controllers\Backend;

use App\Domains\ContentManagement\Models\Article;
use App\Domains\ContentManagement\Services\ArticleContentImageService;
use App\Domains\Gallery\Services\GalleryService;
use App\Domains\Tenant\Services\TenantResolver;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ArticleController extends Controller
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
      $tenants = $this->tenantResolver->availableTenantsForUser(auth()->user())->sortBy('name');
      $selectedTenantId = $tenants->count() === 1 ? $tenants->first()->id : null;

      return view('backend.article.create', compact('tenants', 'selectedTenantId'));
    } catch (\Exception $ex) {
      Log::error('Failed to load article creation page', ['error' => $ex->getMessage()]);
      return abort(500);
    }
  }

  /**
   * Store a newly created resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @param ArticleContentImageService $contentImageService
   * @return \Illuminate\Http\RedirectResponse|void
   */
  public function store(Request $request, ArticleContentImageService $contentImageService)
  {
    $tenantId = $this->resolveTenantId($request);
    if ($tenantId) {
      $request->merge(['tenant_id' => $tenantId]);
    }

    $data = request()->validate([
      'title' => 'required|string|max:255',
      'content' => 'string|required',
      'categories' => 'nullable|string',
      'content_images_json' => 'nullable|string',
      'tenant_id' => ['required', 'exists:tenants,id'],
    ]);

    $data['content'] = $this->sanitizeHtml($data['content']);
    $data['published_at'] = now();
    $data['categories_json'] = $this->parseCategories($request->input('categories'));

    $contentImages = json_decode($request->input('content_images_json'));
    $filteredImages = $contentImageService->filterImagesByContent($contentImages, $data['content']);

    // Delete unused images (images not available in the content)
    $unusedImages = $contentImageService->diffImages($contentImages, $filteredImages);
    $contentImageService->deleteImages($unusedImages);

    // Record the filtered images in DB
    $data['content_images_json'] = $filteredImages;

    try {
      $article = new Article($data);
      $article->created_by = Auth::user()->id;
      $article->save();

      Log::info('Article created', [
        'article_id' => $article->id,
        'tenant_id' => $article->tenant_id,
        'user_id' => Auth::id(),
        'content_images_json' => $data['content_images_json'],
        'filtered_images' => $filteredImages,
      ]);

      if (config('gallery.enabled')) {
        return redirect()->route('dashboard.article.gallery.index', $article)->with('Success', 'Article was created !');
      }

      return redirect()->route('dashboard.article.index')->with('Success', 'Article was created !');
    } catch (\Exception $ex) {
      Log::error('Failed to create article', ['error' => $ex->getMessage()]);
      return abort(500);
    }
  }

  /**
   * Show the form for editing the specified resource.
   *
   * @param \App\Domains\ContentManagement\Models\Article $article
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
   */
  public function edit(Article $article)
  {
    try {
      $tenants = $this->tenantResolver->availableTenantsForUser(auth()->user())->sortBy('name');
      $selectedTenantId = $article->tenant_id;

      return view('backend.article.edit', compact('article', 'tenants', 'selectedTenantId'));
    } catch (\Exception $ex) {
      Log::error('Failed to load article edit page', ['error' => $ex->getMessage()]);
      return abort(500);
    }
  }

  /**
   * Update the specified resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @param \App\Domains\ContentManagement\Models\Article $article
   * @param ArticleContentImageService $contentImageService
   * @return \Illuminate\Http\RedirectResponse
   */
  public function update(Request $request, Article $article, ArticleContentImageService $contentImageService)
  {
    $tenantId = $this->resolveTenantId($request);
    if ($tenantId) {
      $request->merge(['tenant_id' => $tenantId]);
    }

    $data = request()->validate([
      'title' => 'required|string|max:255',
      'content' => 'string|required',
      'categories' => 'nullable|string',
      'content_images_json' => 'nullable|string',
      'tenant_id' => ['required', 'exists:tenants,id'],
    ]);

    $data['content'] = $this->sanitizeHtml($data['content']);
    $data['categories_json'] = $this->parseCategories($request->input('categories'));

    // Normalize the images before compare
    $incomingImages = $contentImageService->normalizeImages(json_decode($request->input('content_images_json')) ?? []);
    $previousImages = $contentImageService->normalizeImages($article->content_images_json ?? []);

    // Find all images referenced in content using both previous and incoming lists (captures newly added uploads).
    $candidateImages = array_merge($previousImages, $incomingImages);
    $usedImages = $contentImageService->filterImagesByContent($candidateImages, $data['content']);

    // Build a fast lookup for used images (prefer id, fall back to url) and remove unused previous images.
    $usedKeys = [];
    $uniqueUsedImages = [];
    foreach ($usedImages as $image) {
      $key = $image->id ?? $image->url ?? json_encode($image);
      if (isset($usedKeys[$key])) {
        continue;
      }
      $usedKeys[$key] = true;
      $uniqueUsedImages[] = $image;
    }

    $unusedImages = [];
    foreach ($previousImages as $image) {
      $key = $image->id ?? $image->url ?? json_encode($image);
      if (! isset($usedKeys[$key])) {
        $unusedImages[] = $image;
      }
    }
    $contentImageService->deleteImages($unusedImages);

    Log::info('Article changes - ' . $article->id, [
      'incoming' => $incomingImages,
      'previous' => $previousImages,
      'used' => $uniqueUsedImages,
      'removed' => $unusedImages,
    ]);

    // Save only the images actually used in the content, including any new ones from the incoming list.
    $data['content_images_json'] = $uniqueUsedImages;

    try {
      $article->update($data);
      $article->updated_by = Auth::user()->id;
      $article->save();

      return redirect()->route('dashboard.article.index')->with('Success', 'Article was updated !');
    } catch (\Exception $ex) {
      Log::error('Failed to update article', ['article_id' => $article->id, 'error' => $ex->getMessage()]);
      return abort(500);
    }
  }

  /**
   * Confirm to delete the specified resource from storage.
   *
   * @param \App\Domains\ContentManagement\Models\Article $article
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
   */
  public function delete(Article $article)
  {
    return view('backend.article.delete', compact('article'));
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param \App\Domains\ContentManagement\Models\Article $article
   * @param \App\Domains\Gallery\Services\GalleryService $galleryService
   * @param ArticleContentImageService $contentImageService
   * @return \Illuminate\Http\RedirectResponse|null
   */
  public function destroy(Article $article, GalleryService $galleryService, ArticleContentImageService $contentImageService)
  {
    try {
      $galleryService->deleteGalleryForImageable($article);
      $contentImageService->deleteImages($article->content_images_json ?? []);
      $article->delete();

      Log::info('Article deleted', [
        'article_id' => $article->id,
        'tenant_id' => $article->tenant_id,
        'user_id' => Auth::id(),
      ]);

      return redirect()->route('dashboard.article.index')->with('Success', 'Article was deleted !');
    } catch (\Exception $ex) {
      Log::error('Failed to delete article', ['article_id' => $article->id, 'error' => $ex->getMessage()]);
      return abort(500);
    }
  }

  private function resolveTenantId(Request $request): ?int
  {
    if ($request->filled('tenant_id')) {
      return (int) $request->input('tenant_id');
    }

    $tenants = $this->tenantResolver->availableTenantsForUser($request->user());

    if ($tenants->count() === 1) {
      return (int) $tenants->first()->id;
    }

    return null;
  }

  private function sanitizeHtml($html): string
  {
    return strip_tags($html, '<p><a><b><i><strong><em><ul><ol><li><br><hr><h1><h2><h3><h4><h5><h6><span><div><img><blockquote><pre><code><table><thead><tbody><tfoot><tr><th><td>');
  }

  private function parseCategories(?string $value): array
  {
    if (! $value) {
      return [];
    }

    $items = array_map('trim', explode(',', $value));
    $items = array_filter($items, fn($item) => $item !== '');

    return array_values(array_unique($items));
  }

  private function decodeContentImages(?string $payload): array
  {
    if (! $payload) {
      return [];
    }

    $decoded = json_decode($payload, true);

    return is_array($decoded) ? $decoded : [];
  }
}
