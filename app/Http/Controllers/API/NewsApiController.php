<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\NewsResource;
use App\Http\Controllers\Controller;
use App\Domains\ContentManagement\Models\News;
use App\Domains\Tenant\Services\TenantResolver;
use Illuminate\Support\Facades\Log;

class NewsApiController extends Controller
{
  public function __construct(private TenantResolver $tenantResolver) {}

  public function index()
  {
    try {
      $perPage = 20;
      $tenant = $this->tenantResolver->resolveDefault();

      if (! $tenant) {
        return NewsResource::collection(collect());
      }

      $news = News::with('gallery')
        ->latest()
        ->where('enabled', 1)
        ->forTenant($tenant)
        ->paginate($perPage);

      return NewsResource::collection($news);
    } catch (\Exception $e) {
      Log::error('Error in NewsApiController@index', ['error' => $e->getMessage()]);
      return response()->json(['message' => 'An error occurred while fetching news'], 500);
    }
  }

  public function show($id)
  {
    try {
      $tenant = $this->tenantResolver->resolveDefault();
      if (! $tenant) {
        return response()->json(['message' => 'News not found'], 404);
      }

      $news = News::with('gallery')->forTenant($tenant)->find($id);
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