<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Domains\Event\Models\Event;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

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
            'title' => ['required'],
            'description' => 'string|required',
            'enabled' => 'nullable',
            'link_url' => 'url',
            'link_caption' => 'string',
            'start_at' => 'date_format:Y-m-d H:i',
            'end_at' => 'date_format:Y-m-d H:i',
            'location' => 'string',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('EventImages', 'public');
        }

        try {
            $event = new Event($data);
            $event->enabled = ($request->enabled != null);
            $event->author = Auth::user()->name;
            $event->save();

            return redirect()->route('dashboard.event.index', $event)->with('Success', 'Event Item was created !');
        } catch (\Exception $ex) {
            \Log::error($ex->getMessage());
            return abort(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Event $announcement
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
            'description' => 'string|required',
            'enabled' => 'nullable',
            'link_url' => 'url',
            'link_caption' => 'string',
            'start_at' => 'date_format:Y-m-d H:i',
            'end_at' => 'date_format:Y-m-d H:i',
            'location' => 'string',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('EventImages', 'public');
        } else {
            $data['image'] = $event->image;
        }

        try {
            $event->enabled = ($request->enabled != null);
            $event->author = Auth::user()->name;
            $event->update($data);
            return redirect()->route('dashboard.event.index')->with('Success', 'Event Item was updated !');
        } catch (\Exception $ex) {
            return abort(500);
        }
    }

    /**
     * Confirm to delete the specified resource from storage.
     *
     * @param \App\Models\Announcement $announcement
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function delete(Event $event)
    {
        return view('backend.event.delete', compact('event'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Event $announcement
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function destroy(Event $event)
    {
        try {
            $event->delete();
            return redirect()->route('dashboard.event.index')->with('Success', 'Event Item was deleted !');
        } catch (\Exception $ex) {
            return abort(500);
        }
    }
}
