<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\Middleware\RequirePassword;

class CustomRequirePassword extends RequirePassword
{


  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @param  string|null  $redirectToRoute
   * @return mixed
   */
  public function handle($request, Closure $next, $redirectToRoute = null)
  {
    // Skip password confirmation in the test environment
    if (app()->environment('testing')) {
      return $next($request);
    }

    // Should ask only if user has password = not signed in with providers
    $hasPassword = $this->hasPassword($request);
    if ($hasPassword) {
      return parent::handle($request, $next, $redirectToRoute);
    }
    return $next($request);
  }

  protected function hasPassword($request)
  {
    return (!in_array($request->user()->provider, ['google']));
  }
}