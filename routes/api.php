<?php

use App\Http\Controllers\API\NewsApiController;
use App\Http\Controllers\API\EventApiController;
use App\Http\Controllers\API\CourseApiController;
use App\Http\Controllers\API\SemesterApiController;

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