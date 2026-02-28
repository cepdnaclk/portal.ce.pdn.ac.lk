<?php

use App\Domains\Email\Models\PortalApp;
use App\Http\Controllers\Backend\EmailServiceController;
use App\Http\Controllers\Backend\PortalAppsController;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

Route::group(['middleware' => ['permission:user.access.services.email', 'tenant.access']], function () {

  // Apps and API keys management routes
  Route::get('services/portal-apps', [PortalAppsController::class, 'index'])
    ->name('services.apps')
    ->breadcrumbs(function (Trail $trail) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Portal Apps'), route('dashboard.services.apps'));
    });

  Route::post('services/portal-apps', [PortalAppsController::class, 'store'])
    ->name('services.apps.store');

  Route::get('services/portal-apps/{portalApp}/keys', [PortalAppsController::class, 'keys'])
    ->name('services.apps.keys')->breadcrumbs(function (Trail $trail, PortalApp $portalApp) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Portal Apps'), route('dashboard.services.apps'))
        ->push($portalApp->name)
        ->push(__('Keys'));
    });

  Route::post('services/portal-apps/{portalApp}/keys', [PortalAppsController::class, 'generateKey'])
    ->name('services.apps.keys.generate');

  Route::post('services/portal-apps/keys/{apiKey}/revoke', [PortalAppsController::class, 'revokeKey'])
    ->name('services.apps.keys.revoke');

  Route::delete('services/portal-apps/{portalApp}', [PortalAppsController::class, 'destroy'])
    ->name('services.apps.destroy');

  // Email service routes
  Route::get('services/email', [EmailServiceController::class, 'history'])
    ->name('services.email.history')
    ->breadcrumbs(function (Trail $trail) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Portal Apps'), route('dashboard.services.apps'))
        ->push(__('Email Service'), route('dashboard.services.email.history'));
    });
});
