<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\EventItem\Models\EventItem;
use Illuminate\Validation\Rule;

class EventItemController extends Controller
{
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $types = EventItem::types();
        return view('backend.event.create', compact('types'));
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
            'link_url' => 'string',
            'link_caption' => 'string',
        ]);
        if($request->hasFile('image')){
            $data['image'] = $request->file('image')->store('EventImages','public');
        }

        try {
            $eventItem = new EventItem($data);
            $eventItem->enabled = ($request->enabled != null);
            $eventItem->save();

            return redirect()->route('dashboard.event.index', $eventItem)->with('Success', 'Event Item was created !');
        } catch (\Exception $ex) {
            return abort(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\EventItem $announcement
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(EventItem $eventItem)
    {
        $types = EventItem::types();
        return view('backend.event.edit', compact('eventItem', 'types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\EventItem $eventItem
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, EventItem $eventItem)
    {
        $data = request()->validate([
            'title' => ['required'],
            'description' => 'string|required',
            'enabled' => 'nullable',
            'link_url' => 'string',
            'link_caption' => 'string',
        ]);
        if($request->hasFile('image')){
            $data['image'] = $request->file('image')->store('EventImages','public');
        }else{
            $data['image'] = $eventItem->image;
        }

        try {
            $eventItem->enabled = ($request->enabled != null);
            $eventItem->update($data);
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
    public function delete(EventItem $eventItem)
    {
        return view('backend.event.delete', compact('eventItem'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\EventItem $announcement
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function destroy(EventItem $eventItem)
    {
        try {
            $eventItem->delete();
            return redirect()->route('dashboard.event.index')->with('Success', 'Event Item was deleted !');
        } catch (\Exception $ex) {
            return abort(500);
        }
    }
}
