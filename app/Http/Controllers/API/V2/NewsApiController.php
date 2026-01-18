<?php

namespace App\Http\Controllers\API\V2;

use App\Domains\ContentManagement\Models\News;
use App\Domains\Tenant\Services\TenantResolver;
use App\Http\Controllers\Controller;
use App\Http\Resources\NewsResource;
use Illuminate\Support\Facades\Log;

class NewsApiController extends Controller
{
  public function __construct(private TenantResolver $tenantResolver) {}

  public function index($tenant_slug)
  {
    try {
      $tenant = $this->tenantResolver->resolveBySlug($tenant_slug);

      if (! $tenant) {
        return response()->json(['message' => 'Tenant not found'], 404);
      }

      $perPage = 20;
      $news = News::with('gallery')
        ->latest()
        ->where('enabled', 1)
        ->forTenant($tenant)
        ->paginate($perPage);

      return NewsResource::collection($news);
    } catch (\Exception $e) {
      Log::error('Error in V2 NewsApiController@index', ['error' => $e->getMessage()]);
      return response()->json(['message' => 'An error occurred while fetching news'], 500);
    }
  }

  public function show($tenant_slug, $id)
  {
    try {
      $tenant = $this->tenantResolver->resolveBySlug($tenant_slug);

      if (! $tenant) {
        return response()->json(['message' => 'Tenant not found'], 404);
      }

      $news = News::with('gallery')->forTenant($tenant)->find($id);
      if ($news) {
        return new NewsResource($news);
      }

      return response()->json(['message' => 'News not found'], 404);
    } catch (\Exception $e) {
      Log::error('Error in V2 NewsApiController@show', ['error' => $e->getMessage()]);
      return response()->json(['message' => 'An error occurred while fetching news'], 500);
    }
  }
}