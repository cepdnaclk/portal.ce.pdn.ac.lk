<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\NewsItem\Models\NewsItem;
use Illuminate\Validation\Rule;

class NewsItemController extends Controller
{

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        $types = NewsItem::types();
        return view('backend.news.create', compact('types'));
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
            'type' => ['required', Rule::in(array_keys(NewsItem::types()))],
            'description' => 'string|required',
            'enabled' => 'nullable',
            'link_url' => 'string',
            'link_caption' => 'string',
        ]);
        if($request->hasFile('image')){
            $data['image'] = $request->file('image')->store('NewsImages','public');
        }

        try {
            $newsItem = new NewsItem($data);
            $newsItem->enabled = ($request->enabled != null);
            $newsItem->save();

            return redirect()->route('dashboard.news.index', $newsItem)->with('Success', 'News Item was created !');
        } catch (\Exception $ex) {
            return abort(500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\NewsItem $announcement
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit(NewsItem $newsItem)
    {
        $types = NewsItem::types();
        return view('backend.news.edit', compact('newsItem', 'types'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\NewsItem $announcement
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, NewsItem $newsItem)
    {
        $data = request()->validate([
            'title' => ['required'],
            'type' => ['required', Rule::in(array_keys(NewsItem::types()))],
            'description' => 'string|required',
            'enabled' => 'nullable',
            'link_url' => 'string',
            'link_caption' => 'string',
        ]);
        if($request->hasFile('image')){
            $data['image'] = $request->file('image')->store('NewsImages','public');
        }else{
            $data['image'] = $newsItem->image;
        }

        try {
            $newsItem->enabled = ($request->enabled != null);
            $newsItem->update($data);
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
    public function delete(NewsItem $newsItem)
    {
        return view('backend.news.delete', compact('newsItem'));
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\NewsItem $announcement
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function destroy(NewsItem $newsItem)
    {
        try {
            $newsItem->delete();
            return redirect()->route('dashboard.news.index')->with('Success', 'News Item was deleted !');
        } catch (\Exception $ex) {
            return abort(500);
        }
    }
}