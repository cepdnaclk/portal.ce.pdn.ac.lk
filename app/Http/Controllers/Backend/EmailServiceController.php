<?php

namespace App\Http\Controllers\Backend;

use App\Domains\Email\Models\EmailDeliveryLog;
use App\Domains\Email\Models\PortalApp;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailServiceController extends Controller
{
  public function history(Request $request)
  {
    $query = EmailDeliveryLog::query()->with('portalApp');

    if ($request->filled('status')) {
      $query->where('status', $request->input('status'));
    }

    if ($request->filled('portal_app')) {
      $query->where('portal_app_id', $request->input('portal_app'));
    }

    if ($request->filled('from_date')) {
      $query->whereDate('created_at', '>=', $request->input('from_date'));
    }

    if ($request->filled('to_date')) {
      $query->whereDate('created_at', '<=', $request->input('to_date'));
    }

    $logs = $query->orderByDesc('created_at')->paginate(20);

    $portalApps = PortalApp::orderBy('name')->get();

    return view('backend.portal-apps.email-service.history')
      ->withLogs($logs)
      ->withPortalApps($portalApps);
  }
}
