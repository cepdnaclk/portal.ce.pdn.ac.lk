<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaxonomyListResource;
use App\Http\Resources\TaxonomyResource;
use App\Http\Resources\TaxonomyTermResource;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
use App\Domains\Tenant\Services\TenantResolver;
use Illuminate\Support\Facades\Log;

class TaxonomyApiController extends Controller
{
  public function __construct(private TenantResolver $tenantResolver) {}

  public function index()
  {
    try {
      $tenant = $this->tenantResolver->resolveDefault();

      if (! $tenant) {
        return response()->json(['status' => 'error', 'message' => 'Default tenant not found'], 404);
      }

      $result = Taxonomy::where('visibility', true)
        ->where('tenant_id', $tenant->id)
        ->get();

      if ($result) {
        return response()->json(
          [
            'status' => 'success',
            'data' => TaxonomyListResource::collection($result)
          ]
        );
      } else {
        return response()->json(['status' => 'error', 'message' => 'No Taxonomies found'], 404);
      }
    } catch (\Exception $e) {
      Log::error('Error in TaxonomyApiController@index', ['error' => $e->getMessage()]);
      return response()->json(['status' => 'error', 'message' => 'An error occurred while fetching taxonomy index'], 500);
    }
  }

  public function get_taxonomy($taxonomy_code)
  {
    try {
      $tenant = $this->tenantResolver->resolveDefault();

      if (! $tenant) {
        return response()->json(['status' => 'error', 'message' => 'Default tenant not found'], 404);
      }

      $result = Taxonomy::where('code', $taxonomy_code)
        ->where('tenant_id', $tenant->id)
        ->first();

      if ($result) {
        if (!$result->visibility) {
          return response()->json(['status' => 'error', 'message' => 'Taxonomy not available'], 404);
        }
        return response()->json(
          [
            'status' => 'success',
            'data' => TaxonomyResource::collection([$result])->resolve()[0]
          ]
        );
      } else {
        return response()->json(['status' => 'error', 'message' => 'Taxonomy not found'], 404);
      }
    } catch (\Exception $e) {
      Log::error('Error in TaxonomyApiController@get_taxonomy', ['error' => $e->getMessage()]);
      return response()->json(['status' => 'error', 'message' => 'An error occurred while fetching a taxonomy'], 500);
    }
  }


  public function get_term($term_code)
  {
    try {
      $tenant = $this->tenantResolver->resolveDefault();

      if (! $tenant) {
        return response()->json(['status' => 'error', 'message' => 'Default tenant not found'], 404);
      }

      $result = TaxonomyTerm::where('code', $term_code)
        ->whereHas('taxonomy', function ($query) use ($tenant) {
          $query->where('tenant_id', $tenant->id);
        })
        ->with('taxonomy')
        ->first();

      if ($result) {
        if ($result->taxonomy && !$result->taxonomy->visibility) {
          return response()->json(['status' => 'error', 'message' => 'Taxonomy term not available'], 404);
        }

        $data = TaxonomyTermResource::collection([$result])->resolve()[0];
        $data['taxonomy'] = $result->taxonomy->code; // Add the taxonomy code at the top-level term

        return response()->json(
          [
            'status' => 'success',
            'data' => $data
          ]
        );
      } else {
        return response()->json(['status' => 'error', 'message' => 'Taxonomy term not found'], 404);
      }
    } catch (\Exception $e) {
      Log::error('Error in TaxonomyApiController@get_term', ['error' => $e->getMessage()]);
      return response()->json(['status' => 'error', 'message' => 'An error occurred while fetching a taxonomy term'], 500);
    }
  }
}