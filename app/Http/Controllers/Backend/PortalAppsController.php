<?php

namespace App\Http\Controllers\Backend;

use App\Domains\Email\Models\ApiKey;
use App\Domains\Email\Models\PortalApp;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PortalAppsController extends Controller
{
  public function index()
  {
    return view('backend.portal-apps.index');
  }

  public function keys(PortalApp $portalApp)
  {
    $portalApp->load(['apiKeys' => function ($query) {
      $query->orderByDesc('created_at');
    }]);

    return view('backend.portal-apps.keys.index', compact('portalApp'));
  }

  public function store(Request $request)
  {
    $data = $request->validate([
      'name' => ['required', 'string', 'max:255'],
      'status' => ['nullable', 'in:active,revoked'],
    ]);

    $data['status'] = $data['status'] ?? PortalApp::STATUS_ACTIVE;

    PortalApp::create($data);

    return redirect()->route('dashboard.services.apps')
      ->with('Success', 'Portal app created.');
  }

  public function generateKey(Request $request, PortalApp $portalApp)
  {
    $data = $request->validate([
      'expires_at' => ['nullable', 'date'],
    ]);

    $expiresAt = isset($data['expires_at']) ? Carbon::parse($data['expires_at']) : null;

    [$apiKey, $plain] = ApiKey::issue($portalApp, $expiresAt);

    return redirect()->route('dashboard.services.apps.keys', $portalApp)
      ->with('Success', 'API key generated. Copy it now; it will not be shown again.')
      ->with('new_api_key', [
        'portal_app_id' => $portalApp->id,
        'key' => $plain,
        'key_id' => $apiKey->id,
      ]);
  }

  public function revokeKey(ApiKey $apiKey)
  {
    $portalApp = $apiKey->portalApp;
    $apiKey->forceFill(['revoked_at' => now()])->save();

    return redirect()->route('dashboard.services.apps.keys', $portalApp)
      ->with('Success', 'API key revoked.');
  }

  public function destroy(PortalApp $portalApp)
  {
    $portalApp->delete();

    return redirect()->route('dashboard.services.apps')
      ->with('Success', 'Portal app deleted.');
  }
}