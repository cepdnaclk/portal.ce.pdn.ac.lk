<?php

use App\Domains\Profile\Http\Controllers\Backend\ProfileController;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

// Onboarding wizard — any authenticated backend user, no special permission
Route::get('profile/onboarding', function () {
  return view('backend.profiles.onboarding');
})->name('profiles.onboarding')
  ->breadcrumbs(function (Trail $trail) {
    $trail->push(__('Home'), route('dashboard.home'))
      ->push(__('Complete Profile'), route('dashboard.profiles.onboarding'));
  });

// User Profiles
Route::group(['middleware' => ['permission:user.access.profiles.editor|user.access.profiles.viewer']], function () {
  // Index
  Route::get('profiles', [ProfileController::class, 'index'])
    ->name('profiles.index')
    ->breadcrumbs(function (Trail $trail) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Profiles'), route('dashboard.profiles.index'));
    });

  // View
  Route::get('profiles/view/{userProfile}', [ProfileController::class, 'show'])
    ->name('profiles.show')
    ->breadcrumbs(function (Trail $trail, $userProfile) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Profiles'), route('dashboard.profiles.index'))
        ->push($userProfile->email)
        ->push(__('View'));
    });

  // Only Editors have access to these functionalities
  Route::group(['middleware' => ['permission:user.access.profiles.editor']], function () {
    // Create
    Route::get('profiles/create', [ProfileController::class, 'create'])
      ->name('profiles.create')
      ->breadcrumbs(function (Trail $trail) {
        $trail->push(__('Home'), route('dashboard.home'))
          ->push(__('Profiles'), route('dashboard.profiles.index'))
          ->push(__('Create'));
      });

    // Store
    Route::post('profiles', [ProfileController::class, 'store'])
      ->name('profiles.store');

    // Merge two profiles
    Route::get('profiles/merge', [ProfileController::class, 'mergeSelect'])
      ->name('profiles.merge.select')
      ->breadcrumbs(function (Trail $trail) {
        $trail->push(__('Home'), route('dashboard.home'))
          ->push(__('Profiles'), route('dashboard.profiles.index'))
          ->push(__('Merge Profiles'));
      });

    Route::post('profiles/merge/review', [ProfileController::class, 'mergeReview'])
      ->name('profiles.merge.review');

    Route::post('profiles/merge', [ProfileController::class, 'mergeStore'])
      ->name('profiles.merge.store');

    // Edit
    Route::get('profiles/edit/{userProfile}', [ProfileController::class, 'edit'])
      ->name('profiles.edit')
      ->breadcrumbs(function (Trail $trail, $userProfile) {
        $trail->push(__('Home'), route('dashboard.home'))
          ->push(__('Profiles'), route('dashboard.profiles.index'))
          ->push($userProfile->email)
          ->push(__('Edit'), route('dashboard.profiles.edit', $userProfile));
      });

    // Update
    Route::put('profiles/{userProfile}', [ProfileController::class, 'update'])
      ->name('profiles.update');

    // Delete confirmation
    Route::get('profiles/delete/{userProfile}', [ProfileController::class, 'delete'])
      ->name('profiles.delete')
      ->breadcrumbs(function (Trail $trail, $userProfile) {
        $trail->push(__('Home'), route('dashboard.home'))
          ->push(__('Profiles'), route('dashboard.profiles.index'))
          ->push($userProfile->email)
          ->push(__('Delete'));
      });

    // Destroy
    Route::delete('profiles/{userProfile}', [ProfileController::class, 'destroy'])
      ->name('profiles.destroy');
  });
});
