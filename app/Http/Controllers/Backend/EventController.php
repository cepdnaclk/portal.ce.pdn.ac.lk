<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Domains\ContentManagement\Models\Event;
use App\Http\Controllers\Controller;
use App\Domains\Gallery\Services\GalleryService;
use App\Domains\Tenant\Services\TenantResolver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class EventController extends Controller
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
      $tenants = $this->tenantResolver->availableTenantsForUser(auth()->user())->sortBy('slug');
      $selectedTenantId = $tenants->count() === 1 ? $tenants->first()->id : null;

      return view('backend.event.create', compact('tenants', 'selectedTenantId'));
    } catch (\Exception $ex) {
      Log::error('Failed to load event creation page', ['error' => $ex->getMessage()]);
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
    $tenantId = $this->resolveTenantId($request);
    if ($tenantId) {
      $request->merge(['tenant_id' => $tenantId]);
    }

    $data = request()->validate([
      'title' => 'string|required',
      'url' => ['required', 'unique:events'],
      'event_type' => 'required|array',
      'published_at' => 'required|date_format:Y-m-d',
      'description' => 'string|required',
      'enabled' => 'nullable',
      'link_url' => 'nullable|url',
      'link_caption' => 'nullable|string',
      'start_at' => 'required|date_format:Y-m-d\\TH:i',
      'end_at' => 'nullable|date_format:Y-m-d\\TH:i',
      'location' => 'string|required',
      'tenant_id' => ['required', 'exists:tenants,id'],
    ]);

    // if ($request->hasFile('image')) {
    //     $data['image'] = $this->uploadThumb(null, $request->image, "events");
    // }

    try {
      $event = new Event($data);
      $event->enabled = ($request->enabled != null);
      $event->url =  urlencode(str_replace(" ", "-", $request->url));
      $event->created_by = Auth::user()->id;
      $event->tenant_id = $data['tenant_id'];
      $event->save();

      if (config('gallery.enabled')) {
        return redirect()->route('dashboard.event.gallery.index', $event)->with('Success', 'Event was created !');
      } else {
        return redirect()->route('dashboard.event.index', $event)->with('Success', 'Event was created !');
      }
    } catch (\Exception $ex) {
      Log::error('Failed to create event', ['error' => $ex->getMessage()]);
      return abort(500);
    }
  }
  /**
   * Show the form for editing the specified resource.
   *
   * @param \App\Domains\ContentManagement\Models\Event $event
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
   */
  public function edit(Event $event)
  {
    $tenants = $this->tenantResolver->availableTenantsForUser(auth()->user())->sortBy('slug');
    $selectedTenantId = $event->tenant_id;

    return view('backend.event.edit', compact('event', 'tenants', 'selectedTenantId'));
  }

  /**
   * Update the specified resource in storage.
   *
   * @param \Illuminate\Http\Request $request
   * @param \App\Domains\ContentManagement\Models\Event $event
   * @return \Illuminate\Http\RedirectResponse
   */
  public function update(Request $request, Event $event)
  {
    $tenantId = $this->resolveTenantId($request);
    if ($tenantId) {
      $request->merge(['tenant_id' => $tenantId]);
    }

    $data = request()->validate([
      'title' => ['required'],
      'url' => ['required', Rule::unique('events')->ignore($event->id)],
      'event_type' => 'required|array',
      'published_at' => 'required|date_format:Y-m-d',
      'description' => 'string|required',
      'enabled' => 'nullable',
      'link_url' => 'nullable|url',
      'link_caption' => 'nullable|string',
      'start_at' => 'required|date_format:Y-m-d\\TH:i',
      'end_at' => 'nullable|date_format:Y-m-d\\TH:i',
      'location' => 'string|required',
      'tenant_id' => ['required', 'exists:tenants,id'],
    ]);

    try {
      $event->update($data);

      $event->enabled = ($request->enabled != null);
      $event->url =  urlencode(str_replace(" ", "-", $request->url));
      $event->tenant_id = $data['tenant_id'];
      $event->save();

      return redirect()->route('dashboard.event.index')->with('Success', 'Event was updated !');
    } catch (\Exception $ex) {
      Log::error('Failed to update event', ['event_id' => $event->id, 'error' => $ex->getMessage()]);
      return abort(500);
    }
  }


  /**
   * Confirm to delete the specified resource from storage.
   *
   * @param \App\Domains\ContentManagement\Models\Event $event
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
   */
  public function delete(Event $event)
  {
    return view('backend.event.delete', compact('event'));
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param \App\Domains\ContentManagement\Models\Event $event
   * @param \App\Domains\Gallery\Services\GalleryService $galleryService
   * @return \Illuminate\Http\RedirectResponse|null
   */
  public function destroy(Event $event, GalleryService $galleryService)
  {
    try {
      // $this->deleteThumb($event->thumbURL());
      $galleryService->deleteGalleryForImageable($event);
      $event->delete();
      return redirect()->route('dashboard.event.index')->with('Success', 'Event was deleted !');
    } catch (\Exception $ex) {
      Log::error('Failed to delete event', ['event_id' => $event->id, 'error' => $ex->getMessage()]);
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
}