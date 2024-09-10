<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\RequirePassword as BaseRequirePassword;

class CustomRequirePassword extends BaseRequirePassword
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