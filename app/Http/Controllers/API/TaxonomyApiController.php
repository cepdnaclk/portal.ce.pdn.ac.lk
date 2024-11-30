<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\TaxonomyResource;
use App\Http\Resources\TaxonomyTermResource;
use App\Domains\Taxonomy\Models\Taxonomy;
use App\Domains\Taxonomy\Models\TaxonomyTerm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TaxonomyApiController extends Controller
{
    public function get_taxonomy($taxonomy_code)
    {
        try {
            $result = Taxonomy::where('code', $taxonomy_code)->first();

            if ($result) {
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
            $result = TaxonomyTerm::where('code', $term_code)->first();

            if ($result) {
                return response()->json(
                    [
                        'status' => 'success',
                        'data' => TaxonomyTermResource::collection([$result])->resolve()[0]
                    ]
                );
            } else {
                return response()->json(['status' => 'error', 'message' => 'Taxonomy term not found'], 404);
            }
        } catch (\Exception $e) {
            Log::error('Error in TaxonomyApiController@get_term', ['error' => $e->getMessage()]);
            return response()->json(['status' => 'error', 'message' => 'An error occurred while fetching a taxonomy'], 500);
        }
    }
}