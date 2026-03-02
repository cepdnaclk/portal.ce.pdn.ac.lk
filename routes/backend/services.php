<?php

use App\Domains\Email\Models\PortalApp;
use App\Http\Controllers\Backend\EmailServiceController;
use App\Http\Controllers\Backend\PortalAppsController;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

Route::group(['middleware' => ['tenant.access']], function () {

  // Apps and API keys management routes
  Route::group(['middleware' => ['permission:user.access.services.apps']], function () {
    Route::get('services/apps', [PortalAppsController::class, 'index'])
      ->name('services.apps')
      ->breadcrumbs(function (Trail $trail) {
        $trail->push(__('Home'), route('dashboard.home'))
          ->push(__('Services'))
          ->push(__('Apps'))
          ->push(__('App Management'));
      });

    Route::post('services/apps', [PortalAppsController::class, 'store'])
      ->name('services.apps.store');

    Route::delete('services/apps/{portalApp}', [PortalAppsController::class, 'destroy'])
      ->name('services.apps.destroy');

    Route::get('services/apps/{portalApp}/keys', [PortalAppsController::class, 'keys'])
      ->name('services.apps.keys')->breadcrumbs(function (Trail $trail, PortalApp $portalApp) {
        $trail->push(__('Home'), route('dashboard.home'))
          ->push(__('Services'))
          ->push(__('Apps'), route('dashboard.services.apps'))
          ->push($portalApp->name)
          ->push(__('Keys'));
      });

    Route::post('services/apps/{portalApp}/keys', [PortalAppsController::class, 'generateKey'])
      ->name('services.apps.keys.generate');

    Route::post('services/apps/keys/{apiKey}/revoke', [PortalAppsController::class, 'revokeKey'])
      ->name('services.apps.keys.revoke');
  });

  Route::group(['middleware' => ['permission:user.access.services.email']], function () {
    Route::get('services/email/history', [EmailServiceController::class, 'history'])
      ->name('services.email.history')
      ->breadcrumbs(function (Trail $trail) {
        $trail->push(__('Home'), route('dashboard.home'))
          ->push(__('Services'))
          ->push(__('Email Service'))
          ->push(__('History'));
      });
  });
});
