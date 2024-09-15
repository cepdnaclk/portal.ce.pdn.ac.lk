<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\Announcement\Models\Announcement;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        Log::debug('Entering AnnouncementController@create');
        $areas = Announcement::areas();
        $types = Announcement::types();
        return view('backend.announcements.create', compact('areas', 'types'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse|void
     */
    public function store(Request $request)
    {
        Log::debug('Entering AnnouncementController@store', ['request' => $request->all()]);
        
        $data = request()->validate([
            'area' => ['required', Rule::in(array_keys(Announcement::areas()))],
            'type' => ['required', Rule::in(array_keys(Announcement::types()))],
            'message' => 'string|required',
            'enabled' => 'nullable',
            'starts_at' => 'required|date_format:Y-m-d\\TH:i',
            'ends_at' => 'required|date_format:Y-m-d\\TH:i', // TODO: Test ends>starts
        ]);

        Log::debug('Validated data', ['data' => $data]);

        try {
            $announcement = new Announcement($data);
            $announcement->enabled = ($request->enabled != null);
            $announcement->save();

            Log::info('Announcement created successfully', ['announcement_id' => $announcement->id]);
            return redirect()->route('dashboard.announcements.index', $announcement)->with('Success', 'Announcement was created !');
        } catch (\Exception $ex) {
            Log::error('Failed to create announcement', ['error' => $ex->getMessage()]);
            return abort(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Announcement $announcement
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(Announcement $announcement)
    {
        Log::debug('Entering AnnouncementController@edit', ['announcement_id' => $announcement->id]);
        $areas = Announcement::areas();
        $types = Announcement::types();
        return view('backend.announcements.edit', compact('announcement', 'areas', 'types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Announcement $announcement
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Announcement $announcement)
    {
        Log::debug('Entering AnnouncementController@update', ['announcement_id' => $announcement->id, 'request' => $request->all()]);
        
        $data = request()->validate([
            'area' => ['required', Rule::in(array_keys(Announcement::areas()))],
            'type' => ['required', Rule::in(array_keys(Announcement::types()))],
            'message' => 'string|required',
            'enabled' => 'nullable',
            'starts_at' => 'required|date_format:Y-m-d\\TH:i',
            'ends_at' => 'required|date_format:Y-m-d\\TH:i', // TODO: Test ends>starts
        ]);

        Log::debug('Validated data', ['data' => $data]);

        try {
            $announcement->enabled = ($request->enabled != null);
            $announcement->update($data);
            Log::info('Announcement updated successfully', ['announcement_id' => $announcement->id]);
            return redirect()->route('dashboard.announcements.index')->with('Success', 'Announcement was updated !');
        } catch (\Exception $ex) {
            Log::error('Failed to update announcement', ['announcement_id' => $announcement->id, 'error' => $ex->getMessage()]);
            return abort(500);
        }
    }

    /**
     * Confirm to delete the specified resource from storage.
     *
     * @param \App\Models\Announcement $announcement
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function delete(Announcement $announcement)
    {
        Log::debug('Entering AnnouncementController@delete', ['announcement_id' => $announcement->id]);
        return view('backend.announcements.delete', compact('announcement'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Announcement $announcement
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function destroy(Announcement $announcement)
    {
        Log::debug('Entering AnnouncementController@destroy', ['announcement_id' => $announcement->id]);
        try {
            $announcement->delete();
            Log::info('Announcement deleted successfully', ['announcement_id' => $announcement->id]);
            return redirect()->route('dashboard.announcements.index')->with('Success', 'Announcement was deleted !');
        } catch (\Exception $ex) {
            Log::error('Failed to delete announcement', ['announcement_id' => $announcement->id, 'error' => $ex->getMessage()]);
            return abort(500);
        }
    }
}