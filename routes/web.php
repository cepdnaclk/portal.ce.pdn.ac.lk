<?php

use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

/*
 * Global Routes
 *
 * Routes that are used between both frontend and backend.
 */

// Switch between the included languages
Route::get('lang/{lang}', [LocaleController::class, 'change'])->name('locale.change');

/*
 * Frontend Routes
 */
Route::group(['as' => 'frontend.'], function () {
    includeRouteFiles(__DIR__ . '/frontend/');
});

Route::group(['prefix' => 'intranet', 'as' => 'intranet.'], function () {
    includeRouteFiles(__DIR__ . '/intranet/');
});

/*
 * Backend Routes
 *
 * These routes can only be accessed by users with type `admin`
 */
Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.', 'middleware' => 'admin'], function () {
    includeRouteFiles(__DIR__ . '/backend/');
});
