<?php

use App\Http\Controllers\Backend\EmailServiceController;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

Route::group(['middleware' => ['permission:user.access.services.email', 'tenant.access']], function () {
  Route::get('services/email', [EmailServiceController::class, 'history'])
    ->name('email-service.history')
    ->breadcrumbs(function (Trail $trail) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Email Service'), route('dashboard.email-service.history'));
    });

  Route::get('services/email/portal-apps', [EmailServiceController::class, 'portalApps'])
    ->name('email-service.senders')
    ->breadcrumbs(function (Trail $trail) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Email Service'), route('dashboard.email-service.history'))
        ->push(__('Portal Apps'), route('dashboard.email-service.senders'));
    });

  Route::post('services/email/portal-apps', [EmailServiceController::class, 'storePortalApp'])
    ->name('email-service.senders.store');

  Route::post('services/email/portal-apps/{portalApp}/keys', [EmailServiceController::class, 'generateKey'])
    ->name('email-service.keys.generate');

  Route::post('services/email/keys/{apiKey}/revoke', [EmailServiceController::class, 'revokeKey'])
    ->name('email-service.keys.revoke');
});
