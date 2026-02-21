<?php

namespace App\Http\Controllers\Backend;

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

      return view('backend.dashboard', [
        'tenants' => $tenants,
        'defaultTenant' => Tenant::default()->slug,
      ]);
    } catch (\Exception $ex) {
      Log::error('Failed to load dashboard', ['error' => $ex->getMessage()]);
      return abort(500);
    }
  }
}
