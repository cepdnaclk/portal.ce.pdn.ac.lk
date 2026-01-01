<?php

use App\Http\Controllers\Frontend\User\AccountController;
use App\Http\Controllers\Frontend\User\DashboardController;
use App\Http\Controllers\Frontend\User\ProfileController;
use Tabuna\Breadcrumbs\Trail;

/*
 * These frontend controllers require the user to be logged in
 * All route names are prefixed with 'frontend.'
 * These routes can not be hit if the user has not confirmed their email
 */

Route::group(['as' => 'user.', 'middleware' => ['auth', 'password.expires', config('boilerplate.access.middleware.verified')]], function () {
  Route::get('', [DashboardController::class, 'index'])
    ->name('index')
    ->breadcrumbs(function (Trail $trail) {
      $trail->parent('frontend.index')
        ->push(__('Intranet'), route('intranet.user.index'));
    });

  Route::get('account', [AccountController::class, 'index'])
    ->name('account')
    ->breadcrumbs(function (Trail $trail) {
      $trail->parent('frontend.index')
        ->push(__('Intranet'), route('intranet.user.index'))
        ->push(__('My Account'), route('intranet.user.account'));
    });

  Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
});