<?php

use App\Http\Controllers\API\NewsApiController;
use App\Http\Controllers\API\EventApiController;
use App\Http\Controllers\API\V2\EventApiController as EventApiV2Controller;
use App\Http\Controllers\API\CourseApiController;
use App\Http\Controllers\API\SemesterApiController;
use App\Http\Controllers\API\TaxonomyApiController;
use App\Http\Controllers\API\V2\AnnouncementApiController as AnnouncementApiV2Controller;
use App\Http\Controllers\API\V2\NewsApiController as NewsApiV2Controller;

// V1 API Routes
Route::group(['prefix' => 'news/v1', 'as' => 'api.news.'], function () {
  Route::get('/', [NewsApiController::class, 'index']);
  Route::get('/{id}', [NewsApiController::class, 'show']);
});

Route::group(['prefix' => 'events/v1', 'as' => 'api.events.'], function () {
  Route::get('', [EventApiController::class, 'index']);
  Route::get('/upcoming', [EventApiController::class, 'upcoming']);
  Route::get('/past', [EventApiController::class, 'past']);
  Route::get('/{id}', [EventApiController::class, 'show']);
});


Route::group(['prefix' => 'academic/v1/undergraduate', 'as' => 'api.academic.undergraduate.'], function () {
  Route::get('/courses', [CourseApiController::class, 'index']);
  Route::get('/semesters', [SemesterApiController::class, 'index']);
});

// TODO: Implement postgraduate courses API
Route::group(['prefix' => 'taxonomy/v1/', 'as' => 'api.taxonomy.'], function () {
  Route::get('/', [TaxonomyApiController::class, 'index'])->name('index');
  Route::get('/{taxonomy_code}', [TaxonomyApiController::class, 'get_taxonomy'])->name('get');
  Route::get(
    'term/{term_code}',
    [TaxonomyApiController::class, 'get_term']
  )->name('term.get');
});


// V2 API Routes
Route::group(['as' => 'api.v2.'], function () {
  Route::group(['prefix' => 'news/v2/{tenant_slug}', 'as' => 'news.'], function () {
    Route::get('/', [NewsApiV2Controller::class, 'index']);
    Route::get('/{id}', [NewsApiV2Controller::class, 'show']);
  });

  Route::group(['prefix' => 'events/v2/{tenant_slug}', 'as' => 'events.'], function () {
    Route::get('', [EventApiV2Controller::class, 'index']);
    Route::get('/upcoming', [EventApiV2Controller::class, 'upcoming']);
    Route::get('/past', [EventApiV2Controller::class, 'past']);
    Route::get('/{id}', [EventApiV2Controller::class, 'show']);
  });

  Route::group(['prefix' => 'announcements/v2/{tenant_slug}', 'as' => 'announcements.'], function () {
    Route::get('', [AnnouncementApiV2Controller::class, 'index']);
  });
});