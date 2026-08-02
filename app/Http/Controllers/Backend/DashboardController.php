<?php

namespace App\Http\Controllers\Backend;

use App\Domains\Profile\Models\UserProfile;
use App\Domains\Profile\Models\UserProfileType;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

/**
 * Class DashboardController.
 */
class DashboardController
{
  /**
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
   */
  public function index(Request $request)
  {
    try {
      // Pass the tenants the user belongs to and the default tenant slug to the view for access control
      $user = $request->user();
      $tenants = $user->tenants()->pluck('slug');

      $profileCounts = null;
      if ($user->hasAnyPermission(['user.access.profiles', 'user.access.profiles.editor', 'user.access.profiles.viewer']) || $user->hasAllAccess()) {
        $profileCounts = [
          'total' => UserProfile::count(),
          'by_type' => UserProfileType::query()
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type'),
        ];
      }

      return view('backend.dashboard', [
        'tenants' => $tenants,
        'defaultTenant' => Tenant::default()->slug,
        'profileCounts' => $profileCounts,
      ]);
    } catch (\Exception $ex) {
      Log::error('Failed to load dashboard', ['error' => $ex->getMessage()]);
      return abort(500);
    }
  }
}
