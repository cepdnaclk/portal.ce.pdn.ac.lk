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
        try {
            $perPage = 20;
            $news = News::latest()->where('enabled', 1)->paginate($perPage);

            return NewsResource::collection($news);
        } catch (\Exception $e) {
            Log::error('Error in NewsApiController@index', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'An error occurred while fetching news'], 500);
        }
    }

    public function show($id)
    {
        try {
            $news = News::find($id);
            if ($news) {
                return new NewsResource($news);
            } else {
                return response()->json(['message' => 'News not found'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error in NewsApiController@show', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'An error occurred while fetching news'], 500);
        }
    }
}