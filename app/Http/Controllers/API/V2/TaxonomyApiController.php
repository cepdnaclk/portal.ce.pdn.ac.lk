<?php

namespace App\Http\Controllers\API\V2;

use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
use App\Domains\Tenant\Services\TenantResolver;
use App\Http\Controllers\Controller;
use App\Http\Resources\TaxonomyListResource;
use App\Http\Resources\TaxonomyTermResource;
use App\Http\Resources\TaxonomyResource;
use Illuminate\Support\Facades\Log;

class TaxonomyApiController extends Controller
{
  public function __construct(private TenantResolver $tenantResolver) {}

  public function index($tenant_slug)
  {
    try {
      $tenant = $this->tenantResolver->resolveBySlug($tenant_slug);

      if (! $tenant) {
        return response()->json(['message' => 'Tenant not found'], 404);
      }

      $taxonomies = Taxonomy::where('visibility', true)
        ->where('tenant_id', $tenant->id)
        ->get();

      return response()->json([
        'status' => 'success',
        'data' => TaxonomyListResource::collection($taxonomies)
      ]);
    } catch (\Exception $e) {
      Log::error('Error in V2 TaxonomyApiController@index', ['error' => $e->getMessage()]);
      return response()->json(['message' => 'An error occurred while fetching taxonomies'], 500);
    }
  }

  public function get_taxonomy($tenant_slug, $taxonomy_code)
  {
    try {
      $tenant = $this->tenantResolver->resolveBySlug($tenant_slug);

      if (! $tenant) {
        return response()->json(['message' => 'Tenant not found'], 404);
      }

      $taxonomy = Taxonomy::where('code', $taxonomy_code)
        ->where('tenant_id', $tenant->id)
        ->where('visibility', true)
        ->first();

      if (!$taxonomy) {
        return response()->json(['message' => 'Taxonomy not found'], 404);
      }

      if (!$taxonomy->visibility) {
        return response()->json(['message' => 'Taxonomy not available'], 404);
      }

      return response()->json(
        [
          'status' => 'success',
          'data' => TaxonomyResource::collection([$taxonomy])->resolve()[0]
        ]
      );
    } catch (\Exception $e) {
      Log::error('Error in V2 TaxonomyApiController@get_taxonomy', ['error' => $e->getMessage()]);
      return response()->json(['message' => 'An error occurred while fetching a taxonomy'], 500);
    }
  }

  public function get_term($tenant_slug, $term_code)
  {
    try {
      $tenant = $this->tenantResolver->resolveBySlug($tenant_slug);

      if (! $tenant) {
        return response()->json(['message' => 'Tenant not found'], 404);
      }

      $term = TaxonomyTerm::where('code', $term_code)
        ->whereHas('taxonomy', function ($query) use ($tenant) {
          $query->where('tenant_id', $tenant->id);
        })
        ->with('taxonomy')
        ->first();

      if (! $term) {
        return response()->json(['message' => 'Taxonomy term not found'], 404);
      }

      if ($term->taxonomy && ! $term->taxonomy->visibility) {
        return response()->json(['message' => 'Taxonomy term not available'], 404);
      }

      $data = TaxonomyTermResource::make($term)->resolve();
      $data['taxonomy'] = $term->taxonomy->code;

      return response()->json([
        'status' => 'success',
        'data' => $data
      ]);
    } catch (\Exception $e) {
      Log::error('Error in V2 TaxonomyApiController@get_term', ['error' => $e->getMessage()]);
      return response()->json(['message' => 'An error occurred while fetching a taxonomy term'], 500);
    }
  }
}