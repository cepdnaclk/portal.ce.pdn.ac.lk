<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Domains\Event\Models\Event;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('backend.event.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function store(Request $request)
    {
        $data = request()->validate([
            'title' => 'string|required',
            'url' => ['required', 'unique:events'],
            'published_at' => 'required|date_format:Y-m-d',
            'description' => 'string|required',
            'enabled' => 'nullable',
            'link_url' => 'nullable|url',
            'link_caption' => 'nullable|string',
            'start_at' => 'required|date_format:Y-m-d\\TH:i',
            'end_at' => 'nullable|date_format:Y-m-d\\TH:i',
            'location' => 'string|required',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadThumb(null, $request->image, "events");
        }

        try {
            $event = new Event($data);
            $event->enabled = ($request->enabled != null);
            $event->url =  urlencode(str_replace(" ", "-", $request->url));
            $event->created_by = Auth::user()->id;
            $event->save();

            return redirect()->route('dashboard.event.index', $event)->with('Success', 'Event was created !');
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
            return abort(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Event $event
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Event $event)
    {
        return view('backend.event.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Event $event
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Event $event)
    {
        $data = request()->validate([
            'title' => ['required'],
            'url' =>
            ['required', Rule::unique('event')->ignore($event->id)],
            'published_at' => 'required|date_format:Y-m-d',
            'description' => 'string',
            'enabled' => 'nullable',
            'link_url' => 'nullable|url',
            'link_caption' => 'nullable|string',
            'start_at' => 'required|date_format:Y-m-d\\TH:i',
            'end_at' => 'nullable|date_format:Y-m-d\\TH:i',
            'location' => 'string|required',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadThumb($event->image, $request->image, "events");
        } else {
            $data['image'] = $event->image;
        }

        try {
            $event->update($data);
            $event->enabled = ($request->enabled != null);
            $event->url =  urlencode(str_replace(" ", "-", $request->url));
            $event->created_by = Auth::user()->id;
            $event->save();

            return redirect()->route('dashboard.event.index')->with('Success', 'Event was updated !');
        } catch (\Exception $ex) {
            return abort(500);
        }
    }

    /**
     * Confirm to delete the specified resource from storage.
     *
     * @param \App\Models\Event $event
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function delete(Event $event)
    {
        return view('backend.event.delete', compact('event'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Event $event
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function destroy(Event $event)
    {
        try {
            $event->delete();
            return redirect()->route('dashboard.event.index')->with('Success', 'Event was deleted !');
        } catch (\Exception $ex) {
            return abort(500);
        }
    }

    // Private function to handle deleting images
    private function deleteThumb($currentURL)
    {
        if ($currentURL != null) {
            $oldImage = public_path($currentURL);
            if (File::exists($oldImage)) unlink($oldImage);
        }
    }

    // Private function to handle uploading  images
    private function uploadThumb($currentURL, $newImage, $folder)
    {
        // Delete the existing image
        $this->deleteThumb($currentURL);

        $imageName = time() . '.' . $newImage->extension();
        $newImage->move(public_path('img/' . $folder), $imageName);
        $imagePath = "/img/$folder/" . $imageName;
        $image = Image::make(public_path($imagePath));
        $image->save();

        return $imageName;
    }
}