<?php

namespace App\Http\Controllers\Backend;

use App\Domains\Email\Models\ApiKey;
use App\Domains\Email\Models\EmailDeliveryLog;
use App\Domains\Email\Models\PortalApp;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class EmailServiceController extends Controller
{
  public function history(Request $request)
  {
    $query = EmailDeliveryLog::query()->with('senderAccount');

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

  public function portalApps()
  {
    $portalApps = PortalApp::with(['apiKeys' => function ($query) {
      $query->orderByDesc('created_at');
    }])->orderBy('name')->get();

    return view('backend.portal-apps.email-service.senders')->withPortalApps($portalApps);
  }

  public function storePortalApp(Request $request)
  {
    $data = $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'status' => ['nullable', 'in:active,revoked'],
    ]);

    $data['status'] = $data['status'] ?? PortalApp::STATUS_ACTIVE;

    PortalApp::create($data);

    return redirect()->route('dashboard.email-service.senders')
      ->with('Success', 'Portal app created.');
  }

  public function generateKey(Request $request, PortalApp $portalApp)
  {
    $data = $request->validate([
      'expires_at' => ['nullable', 'date'],
    ]);

    $expiresAt = isset($data['expires_at']) ? Carbon::parse($data['expires_at']) : null;

    [$apiKey, $plain] = ApiKey::issue($portalApp, $expiresAt);

    return redirect()->route('dashboard.email-service.senders')
      ->with('Success', 'API key generated. Copy it now; it will not be shown again.')
      ->with('new_api_key', [
        'portal_app_id' => $portalApp->id,
        'key' => $plain,
        'key_id' => $apiKey->id,
      ]);
  }

  public function revokeKey(ApiKey $apiKey)
  {
    $apiKey->forceFill(['revoked_at' => now()])->save();

    return redirect()->route('dashboard.email-service.senders')
      ->with('Success', 'API key revoked.');
  }
}
