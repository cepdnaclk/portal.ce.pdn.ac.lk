<?php

namespace App\Http\Controllers\API\V2;

use App\Domains\ContentManagement\Models\Article;
use App\Domains\Tenant\Services\TenantResolver;
use App\Http\Controllers\Controller;
use App\Http\Resources\ArticleResource;
use Illuminate\Support\Facades\Log;

class ArticleApiController extends Controller
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
      $articles = Article::with(['gallery', 'author'])
        ->latest()
        ->enabled()
        ->forTenant($tenant)
        ->paginate($perPage);

      return ArticleResource::collection($articles);
    } catch (\Exception $e) {
      Log::error('Error in V2 ArticleApiController@index', ['error' => $e->getMessage()]);
      return response()->json(['message' => 'An error occurred while fetching articles'], 500);
    }
  }

  public function show($tenant_slug, $id)
  {
    try {
      $tenant = $this->tenantResolver->resolveBySlug($tenant_slug);

      if (! $tenant) {
        return response()->json(['message' => 'Tenant not found'], 404);
      }

      $article = Article::with(['gallery', 'author'])
        ->enabled()
        ->forTenant($tenant)
        ->find($id);
      if ($article) {
        return new ArticleResource($article);
      }

      return response()->json(['message' => 'Article not found'], 404);
    } catch (\Exception $e) {
      Log::error('Error in V2 ArticleApiController@show', ['error' => $e->getMessage()]);
      return response()->json(['message' => 'An error occurred while fetching articles'], 500);
    }
  }

  public function category($tenant_slug, string $category)
  {
    try {
      $tenant = $this->tenantResolver->resolveBySlug($tenant_slug);

      if (! $tenant) {
        return response()->json(['message' => 'Tenant not found'], 404);
      }

      $perPage = 20;
      $articles = Article::with(['gallery', 'author'])
        ->latest()
        ->enabled()
        ->forTenant($tenant)
        ->withCategory($category)
        ->paginate($perPage);

      return ArticleResource::collection($articles);
    } catch (\Exception $e) {
      Log::error('Error in V2 ArticleApiController@category', ['error' => $e->getMessage()]);
      return response()->json(['message' => 'An error occurred while fetching articles'], 500);
    }
  }
}
