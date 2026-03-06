<?php

namespace App\Http\Middleware;

use App\Domains\Email\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ApiKeyAuthenticate
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
    $providedKey = $request->header('X-API-KEY');

    if (!$providedKey) {
      $this->logFailure($request, 'missing_key');
      return response()->json(['message' => 'Unauthorized.'], Response::HTTP_UNAUTHORIZED);
    }

    $hash = ApiKey::hashKey($providedKey);
    $apiKey = ApiKey::where('key_hash', $hash)->with('portalApp')->first();

    if (!$apiKey || !hash_equals($apiKey->key_hash, $hash)) {
      $this->logFailure($request, 'invalid_key', $hash);
      return response()->json(['message' => 'Unauthorized.'], Response::HTTP_UNAUTHORIZED);
    }

    if (!$apiKey->isActive()) {
      $this->logFailure($request, 'revoked_or_expired', $hash);
      return response()->json(['message' => 'Unauthorized. API key was expired or revoked.'], Response::HTTP_UNAUTHORIZED);
    }

    $apiKey->forceFill(['last_used_at' => now()])->save();

    $request->attributes->set('apiKey', $apiKey);
    $request->attributes->set('portalApp', $apiKey->portalApp);

    return $next($request);
  }

  protected function logFailure(Request $request, string $reason, ?string $hash = null): void
  {
    Log::warning('Email API auth failed', [
      'reason' => $reason,
      'ip' => $request->ip(),
      'user_agent' => $request->userAgent(),
      'key_fingerprint' => $hash ? substr($hash, 0, 12) : null,
    ]);
  }
}