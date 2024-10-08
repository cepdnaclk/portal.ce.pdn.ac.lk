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
        try {
            $areas = Announcement::areas();
            $types = Announcement::types();
            return view('backend.announcements.create', compact('areas', 'types'));
        } catch (\Exception $ex) {
            Log::error('Failed to load announcement creation page', ['error' => $ex->getMessage()]);
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
        $data = request()->validate([
            'area' => ['required', Rule::in(array_keys(Announcement::areas()))],
            'type' => ['required', Rule::in(array_keys(Announcement::types()))],
            'message' => 'string|required',
            'enabled' => 'nullable',
            'starts_at' => 'required|date_format:Y-m-d\\TH:i',
            'ends_at' => 'required|date_format:Y-m-d\\TH:i', // TODO: Test ends>starts
        ]);

        try {
            $announcement = new Announcement($data);
            $announcement->enabled = ($request->enabled != null);
            $announcement->save();

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
        try {
            $areas = Announcement::areas();
            $types = Announcement::types();
            return view('backend.announcements.edit', compact('announcement', 'areas', 'types'));
        } catch (\Exception $ex) {
            Log::error('Failed to load announcement edit page', ['announcement_id' => $announcement->id, 'error' => $ex->getMessage()]);
            return abort(500);
        }
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
        $data = request()->validate([
            'area' => ['required', Rule::in(array_keys(Announcement::areas()))],
            'type' => ['required', Rule::in(array_keys(Announcement::types()))],
            'message' => 'string|required',
            'enabled' => 'nullable',
            'starts_at' => 'required|date_format:Y-m-d\\TH:i',
            'ends_at' => 'required|date_format:Y-m-d\\TH:i', // TODO: Test ends>starts
        ]);

        try {
            $announcement->enabled = ($request->enabled != null);
            $announcement->update($data);
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
        try {
            $announcement->delete();
            return redirect()->route('dashboard.announcements.index')->with('Success', 'Announcement was deleted !');
        } catch (\Exception $ex) {
            Log::error('Failed to delete announcement', ['announcement_id' => $announcement->id, 'error' => $ex->getMessage()]);
            return abort(500);
        }
    }
}