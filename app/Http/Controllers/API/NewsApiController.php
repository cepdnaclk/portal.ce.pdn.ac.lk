<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\NewsResource;
use App\Http\Controllers\Controller;
use App\Domains\News\Models\News;
use Illuminate\Support\Facades\Log;

class NewsApiController extends Controller
{
    public function index()
    {
        Log::debug('Entering NewsApiController@index');
        $perPage = 20;
        $news = News::latest()->where('enabled', 1)->paginate($perPage);

        Log::debug('News fetched', ['count' => $news->count()]);

        if ($news->count() > 0) {
            Log::info('Returning news items', ['count' => $news->count()]);
            return NewsResource::collection($news);
        } else {
            Log::warning('No news items found');
            return response()->json(['message' => 'News not found'], 404);
        }
    }

    public function show($id)
    {
        Log::debug('Entering NewsApiController@show', ['id' => $id]);
        $news = News::find($id);

        if ($news) {
            Log::info('News item found', ['id' => $id]);
            return new NewsResource($news);
        } else {
            Log::warning('News item not found', ['id' => $id]);
            return response()->json(['message' => 'News not found'], 404);
        }
    }
}