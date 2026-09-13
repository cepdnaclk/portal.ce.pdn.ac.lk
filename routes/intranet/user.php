<?php

use App\Domains\Profile\Http\Controllers\Frontend\MyProfileController;
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

  // Self-service user profile (UserProfile domain)
  Route::get('profile/manage', [MyProfileController::class, 'edit'])
    ->name('profile.manage')
    ->breadcrumbs(function (Trail $trail) {
      $trail->parent('frontend.index')
        ->push(__('Intranet'), route('intranet.user.index'))
        ->push(__('My Account'), route('intranet.user.account'))
        ->push(__('My Profile'), route('intranet.user.profile.manage'));
    });

  Route::patch('profile/manage', [MyProfileController::class, 'update'])->name('profile.manage.update');
});