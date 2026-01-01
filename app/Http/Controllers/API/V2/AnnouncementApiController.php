<?php

namespace App\Http\Controllers\API\V2;

use App\Domains\Announcement\Models\Announcement;
use App\Domains\Tenant\Services\TenantResolver;
use App\Http\Controllers\Controller;
use App\Http\Resources\AnnouncementResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AnnouncementApiController extends Controller
{
  public function __construct(private TenantResolver $tenantResolver) {}

  public function index(Request $request, $tenant_slug)
  {
    try {
      $tenant = $this->tenantResolver->resolveBySlug($tenant_slug);

      if (! $tenant) {
        return response()->json(['message' => 'Tenant not found'], 404);
      }

      $area = $request->query('area', Announcement::TYPE_FRONTEND);
      $validAreas = [Announcement::TYPE_FRONTEND, Announcement::TYPE_BACKEND];
      if (! in_array($area, $validAreas, true)) {
        $area = Announcement::TYPE_FRONTEND;
      }

      $announcements = Announcement::query()
        ->enabled()
        ->forTenant($tenant)
        ->forArea($area)
        ->inTimeFrame()
        ->orderBy('starts_at', 'desc')
        ->orderBy('created_at', 'desc')
        ->get();

      return AnnouncementResource::collection($announcements);
    } catch (\Exception $e) {
      Log::error('Error in V2 AnnouncementApiController@index', ['error' => $e->getMessage()]);
      return response()->json(['message' => 'An error occurred while fetching announcements'], 500);
    }
  }
}