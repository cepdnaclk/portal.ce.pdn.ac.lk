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

/**
 * Intranet Routes
 */
Route::group(['prefix' => 'intranet', 'as' => 'intranet.', 'middleware' => 'auth'], function () {
    includeRouteFiles(__DIR__ . '/intranet/');
});

/*
 * Backend Routes
 */
Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.', 'middleware' => 'auth'], function () {
    includeRouteFiles(__DIR__ . '/backend/');
});
