<?php

namespace App\Http\Controllers\Backend;

use App\Http\Resources\NewsResource;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Domains\NewsItem\Models\NewsItem;

class NewsApiController extends Controller
{
    public function index()
    {
        $perPage = 20; 
        $news = NewsItem::latest()->paginate($perPage);

        if ($news->count() > 0) {
            return NewsResource::collection($news);
        } else {
            return response()->json(['message' => 'News item not found'], 404);
        }
    }


    public function show($id)
    {
        $news = NewsItem::find($id);

        if ($news) {
            return new NewsResource($news);
        } else {
            return response()->json(['message' => 'News item not found'], 404);
        }
    }
}
