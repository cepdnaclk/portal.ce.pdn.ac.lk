<?php

use App\Http\Controllers\Backend\TaxonomyFileController;
use App\Http\Controllers\Backend\TaxonomyPageController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

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

// Download

Route::group(
    ['prefix' => 'download', 'as' => 'download.'],
    function () {
        Route::get('taxonomy/{file_name}.{extension}', [TaxonomyFileController::class, 'download'])
            ->name('taxonomy-file')
            ->withoutMiddleware(['permission:user.access.taxonomy.file.editor|user.access.taxonomy.file.viewer']);

        Route::get('taxonomy-page/{slug}', [TaxonomyPageController::class, 'download'])
            ->name('taxonomy-page')
            ->withoutMiddleware(['permission:user.access.taxonomy.page.editor|user.access.taxonomy.page.viewer']);
    }
);
