<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\News\Models\News;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('backend.news.create');
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
            'link_url' => 'nullable|url',
            'link_caption' => 'nullable|string',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('NewsImages', 'public');
        }

        try {
            $news = new News($data);
            $news->enabled = ($request->enabled != null);
            $news->author = Auth::user()->name;
            $news->save();

            return redirect()->route('dashboard.news.index', $news)->with('Success', 'News Item was created !');
        } catch (\Exception $ex) {
            return abort(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\News $announcement
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(News $news)
    {
        return view('backend.news.edit', compact('news'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\News $announcement
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, News $news)
    {
        $data = request()->validate([
            'title' => ['required'],
            'description' => 'string|required',
            'enabled' => 'nullable',
            'link_url' => 'nullable|url',
            'link_caption' => 'nullable|string',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('NewsImages', 'public');
        } else {
            $data['image'] = $news->image;
        }

        try {
            $news->enabled = ($request->enabled != null);
            $news->author = Auth::user()->name;
            $news->update($data);
            return redirect()->route('dashboard.news.index')->with('Success', 'News Item was updated !');
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
    public function delete(News $news)
    {
        return view('backend.news.delete', compact('news'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\News $announcement
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function destroy(News $news)
    {
        try {
            $news->delete();
            return redirect()->route('dashboard.news.index')->with('Success', 'News Item was deleted !');
        } catch (\Exception $ex) {
            return abort(500);
        }
    }
}
