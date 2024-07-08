<?php

namespace App\Http\Controllers\Backend;

use App\Http\Resources\NewsResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domains\News\Models\News;

class NewsApiController extends Controller
{
    public function index()
    {
        $perPage = 20;
        $news = News::latest()->paginate($perPage);

        if ($news->count() > 0) {
            return NewsResource::collection($news);
        } else {
            return response()->json(['message' => 'News item not found'], 404);
        }
    }


    public function show($id)
    {
        $news = News::find($id);

        if ($news) {
            return new NewsResource($news);
        } else {
            return response()->json(['message' => 'News item not found'], 404);
        }
    }
}
