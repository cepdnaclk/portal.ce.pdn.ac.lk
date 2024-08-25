<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Domains\News\Models\News;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

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
            'title' => 'required',
            'url' => ['required', 'unique:news'],
            'published_at' => 'required|date_format:Y-m-d',
            'description' => 'string|required',
            'enabled' => 'nullable',
            'link_url' => 'nullable|url',
            'link_caption' => 'nullable|string',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadThumb(null, $request->image, "news");
        }

        try {
            $news = new News($data);
            $news->enabled = ($request->enabled == 1);
            $news->user_id = Auth::user()->id;
            $news->save();

            return redirect()->route('dashboard.news.index', $news)->with('Success', 'News was created !');
        } catch (\Exception $ex) {
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
        return view('backend.news.edit', compact('news'));
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
        $data = request()->validate([
            'title' => ['required'],
            'url' => ['required', Rule::unique('news')->ignore($news->id)],
            'published_at' => 'required|date_format:Y-m-d',
            'description' => 'string|required',
            'enabled' => 'nullable',
            'link_url' => 'nullable|url',
            'link_caption' => 'nullable|string',
        ]);
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadThumb($news->image, $request->image, $news);
        } else {
            $data['image'] = $news->image;
        }

        try {
            $news->enabled = ($request->enabled != null);
            $news->user_id = Auth::user()->id;
            $news->update($data);
            return redirect()->route('dashboard.news.index')->with('Success', 'News was updated !');
        } catch (\Exception $ex) {
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
     * @param \App\Models\News $news
     * @return \Illuminate\Http\RedirectResponse|null
     */
    public function destroy(News $news)
    {
        try {

            $this->deleteThumb($news->thumb);
            $news->delete();
            return redirect()->route('dashboard.news.index')->with('Success', 'News was deleted !');
        } catch (\Exception $ex) {
            return abort(500);
        }
    }

    // Private function to handle deleting images
    private function deleteThumb($currentURL)
    {
        if ($currentURL != null && $currentURL != config('constants.frontend.dummy_thumb')) {
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
        $image = Image::make(public_path($imagePath))->fit(1280, 720);
        $image->save();

        return $imageName;
    }
}