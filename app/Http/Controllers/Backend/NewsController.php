<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\ContentManagement\Models\News;
use App\Domains\Gallery\Services\GalleryService;
use App\Domains\Tenant\Services\TenantResolver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class NewsController extends Controller
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

      return view('backend.news.create', compact('tenants', 'selectedTenantId'));
    } catch (\Exception $ex) {
      Log::error('Failed to load news creation page', ['error' => $ex->getMessage()]);
      return abort(500);
    }
  }
  /**
   * Store a newly created resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @return \Illuminate\Http\RedirectResponse|void
   */
  public function store(Request $request)
  {
    $tenantId = $this->resolveTenantId($request, $this->tenantResolver);
    if ($tenantId) {
      $request->merge(['tenant_id' => $tenantId]);
    }

    $data = request()->validate([
      'title' => 'required',
      'url' => ['required', 'unique:news'],
      'published_at' => 'required|date_format:Y-m-d',
      'description' => 'string|required',
      'enabled' => 'nullable',
      'link_url' => 'nullable|url',
      'link_caption' => 'nullable|string',
      'tenant_id' => ['required', 'exists:tenants,id'],
    ]);

    // if ($request->hasFile('image')) {
    //     $data['image'] = $this->uploadThumb(null, $request->image, "news");
    // }

    try {
      $news = new News($data);
      $news->enabled = ($request->enabled == 1);
      $news->url =  urlencode(str_replace(" ", "-", $request->url)); // TODO other corrections
      $news->created_by = Auth::user()->id;
      $news->author_id = Auth::user()->id;
      $news->save();


      if (config('gallery.enabled_models.news')) {
        return redirect()->route('dashboard.news.gallery.index', $news)->with('Success', 'News was created !');
      } else {
        return redirect()->route('dashboard.news.index', $news)->with('Success', 'News was created !');
      }
    } catch (\Exception $ex) {
      Log::error('Failed to create news', ['error' => $ex->getMessage()]);
      return abort(500);
    }
  }
  /**
   * Show the form for editing the specified resource.
   *
   * @param \App\Models\News $news
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
   */
  public function edit(News $news)
  {
    try {
      $tenants = $this->tenantResolver->availableTenantsForUser(auth()->user())->sortBy('name');
      $selectedTenantId = $news->tenant_id;

      $authorOptions = $this->getAuthorOptions();

      return view('backend.news.edit', compact('news', 'tenants', 'selectedTenantId', 'authorOptions'));
    } catch (\Exception $ex) {
      Log::error('Failed to load news edit page', ['error' => $ex->getMessage()]);
      return abort(500);
    }
  }
  /**
   * Update the specified resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @param \App\Models\News $news
   * @return \Illuminate\Http\RedirectResponse
   */
  public function update(Request $request, News $news)
  {
    $tenantId = $this->resolveTenantId($request, $this->tenantResolver);
    if ($tenantId) {
      $request->merge(['tenant_id' => $tenantId]);
    }

    $data = request()->validate([
      'title' => ['required'],
      'url' => ['required', Rule::unique('news')->ignore($news->id)],
      'published_at' => 'required|date_format:Y-m-d',
      'description' => 'string|required',
      'enabled' => 'nullable',
      'link_url' => 'nullable|url',
      'link_caption' => 'nullable|string',
      'tenant_id' => ['required', 'exists:tenants,id'],
      'author_id' => ['required', 'exists:users,id'],
    ]);

    try {
      $news->update($data);
      $news->enabled = ($request->enabled != null);
      $news->url =  urlencode(str_replace(" ", "-", $request->url)); // TODO other corrections
      $news->save();

      return redirect()->route('dashboard.news.index')->with('Success', 'News was updated !');
    } catch (\Exception $ex) {
      Log::error('Failed to update news', ['news_id' => $news->id, 'error' => $ex->getMessage()]);
      return abort(500);
    }
  }

  /**
   * Confirm to delete the specified resource from storage.
   *
   * @param \App\Models\News $news
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
   */
  public function delete(News $news)
  {
    return view('backend.news.delete', compact('news'));
  }


  /**
   * Remove the specified resource from storage.
   *
   * @param \App\Domains\ContentManagement\Models\News $news
   * @param \App\Domains\Gallery\Services\GalleryService $galleryService
   * @return \Illuminate\Http\RedirectResponse|null
   */
  public function destroy(News $news, GalleryService $galleryService)
  {
    try {
      // $this->deleteThumb($news->thumbURL());
      $galleryService->deleteGalleryForImageable($news);
      $news->delete();


      return redirect()->route('dashboard.news.index')->with('Success', 'News was deleted !');
    } catch (\Exception $ex) {
      Log::error('Failed to delete news', ['news_id' => $news->id, 'error' => $ex->getMessage()]);
      return abort(500);
    }
  }

}
