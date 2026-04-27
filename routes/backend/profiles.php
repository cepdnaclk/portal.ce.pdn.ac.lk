<?php

use App\Domains\Profiles\Models\Profile;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

Route::group([
  'prefix' => 'profiles',
  'as' => 'profiles.',
  'middleware' => ['permission:user.access.profiles.view|user.access.profiles.edit|user.access.profiles.delete'],
], function () {
  Route::get('/', [ProfileController::class, 'index'])
    ->name('index')
    ->breadcrumbs(function (Trail $trail) {
      $trail->parent('dashboard.home')
        ->push(__('Profile Management'), route('dashboard.profiles.index'));
    });

  Route::get('create', [ProfileController::class, 'create'])
    ->middleware('permission:user.access.profiles.edit')
    ->name('create')
    ->breadcrumbs(function (Trail $trail) {
      $trail->parent('dashboard.profiles.index')
        ->push(__('Create Profile'), route('dashboard.profiles.create'));
    });

  Route::post('/', [ProfileController::class, 'store'])
    ->middleware('permission:user.access.profiles.edit')
    ->name('store');

  Route::group(['prefix' => '{profile}'], function () {
    Route::get('delete', [ProfileController::class, 'delete'])
      ->middleware('permission:user.access.profiles.delete')
      ->name('delete')
      ->breadcrumbs(function (Trail $trail, Profile $profile) {
        $trail->parent('dashboard.profiles.index')
          ->push($profile->profile_label(), route('dashboard.profiles.edit', $profile))
          ->push(__('Delete'), route('dashboard.profiles.delete', $profile));
      });

    Route::get('edit', [ProfileController::class, 'edit'])
      ->middleware('permission:user.access.profiles.edit')
      ->name('edit')
      ->breadcrumbs(function (Trail $trail, Profile $profile) {
        $trail->parent('dashboard.profiles.index')
          ->push($profile->profile_label(), route('dashboard.profiles.edit', $profile));
      });

    Route::patch('/', [ProfileController::class, 'update'])
      ->middleware('permission:user.access.profiles.edit')
      ->name('update');

    Route::delete('/', [ProfileController::class, 'destroy'])
      ->middleware('permission:user.access.profiles.delete')
      ->name('destroy');

    Route::get('history', [ProfileController::class, 'history'])
      ->name('history')
      ->breadcrumbs(function (Trail $trail, Profile $profile) {
        $trail->parent('dashboard.profiles.index')
          ->push($profile->profile_label(), route('dashboard.profiles.edit', $profile))
          ->push('History');
      });
  });
});

Route::group([
  'prefix' => 'my-profiles',
  'as' => 'my-profiles.',
  'middleware' => ['auth', 'throttle:60,1'],
], function () {
  Route::get('/', [ProfileController::class, 'myProfiles'])
    ->name('index')
    ->breadcrumbs(function (Trail $trail) {
      $trail->parent('dashboard.home')
        ->push(__('My Profiles'), route('dashboard.my-profiles.index'));
    });

  Route::get('create', [ProfileController::class, 'createMyProfile'])
    ->name('create')
    ->breadcrumbs(function (Trail $trail) {
      $trail->parent('dashboard.my-profiles.index')
        ->push(__('Create My Profile'), route('dashboard.my-profiles.create'));
    });

  Route::post('/', [ProfileController::class, 'storeMyProfile'])->name('store');

  Route::group(['prefix' => '{profile}'], function () {
    Route::get('/', [ProfileController::class, 'editMyProfile'])
      ->name('edit')
      ->breadcrumbs(function (Trail $trail, Profile $profile) {
        $trail->parent('dashboard.my-profiles.index')
          ->push($profile->profile_label(), route('dashboard.my-profiles.edit', $profile));
      });

    Route::patch('/', [ProfileController::class, 'updateMyProfile'])->name('update');

    Route::get('history', [ProfileController::class, 'myHistory'])
      ->name('history')
      ->breadcrumbs(function (Trail $trail, Profile $profile) {
        $trail->parent('dashboard.my-profiles.index')
          ->push($profile->profile_label(), route('dashboard.my-profiles.edit', $profile))
          ->push('History');
      });
  });
});
