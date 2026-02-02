<?php

namespace App\Http\Controllers\API;

use App\Domains\ContentManagement\Models\Article;
use App\Domains\Tenant\Services\TenantResolver;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use Illuminate\Support\Facades\Log;

class ArticleApiController extends Controller
{
  public function __construct(private TenantResolver $tenantResolver) {}

  public function index()
  {
    try {
      $perPage = 20;
      $tenant = $this->tenantResolver->resolveDefault();

      if (! $tenant) {
        return ArticleResource::collection(collect());
      }

      $articles = Article::with(['gallery', 'user'])
        ->latest()
        ->enabled()
        ->forTenant($tenant)
        ->paginate($perPage);

      return ArticleResource::collection($articles);
    } catch (\Exception $e) {
      Log::error('Error in ArticleApiController@index', ['error' => $e->getMessage()]);
      return response()->json(['message' => 'An error occurred while fetching articles'], 500);
    }
  }

  public function show($id)
  {
    try {
      $tenant = $this->tenantResolver->resolveDefault();
      if (! $tenant) {
        return response()->json(['message' => 'Article not found'], 404);
      }

      $article = Article::with(['gallery', 'user'])
        ->enabled()
        ->forTenant($tenant)
        ->find($id);
      if ($article) {
        return new ArticleResource($article);
      }

      return response()->json(['message' => 'Article not found'], 404);
    } catch (\Exception $e) {
      Log::error('Error in ArticleApiController@show', ['error' => $e->getMessage()]);
      return response()->json(['message' => 'An error occurred while fetching articles'], 500);
    }
  }

  public function category(string $category)
  {
    try {
      $perPage = 20;
      $tenant = $this->tenantResolver->resolveDefault();

      if (! $tenant) {
        return ArticleResource::collection(collect());
      }

      $articles = Article::with(['gallery', 'user'])
        ->latest()
        ->enabled()
        ->forTenant($tenant)
        ->withCategory($category)
        ->paginate($perPage);

      return ArticleResource::collection($articles);
    } catch (\Exception $e) {
      Log::error('Error in ArticleApiController@category', ['error' => $e->getMessage()]);
      return response()->json(['message' => 'An error occurred while fetching articles'], 500);
    }
  }
}