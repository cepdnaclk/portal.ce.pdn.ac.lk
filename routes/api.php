<?php

use App\Http\Controllers\API\NewsApiController;
use App\Http\Controllers\API\EventApiController;


Route::group(['prefix' => 'news', 'as' => 'api.news.'], function () {
    Route::get('/', [NewsApiController::class, 'index']);
    Route::get('/{id}', [NewsApiController::class, 'show']);
});

Route::group(['prefix' => 'events', 'as' => 'api.events.'], function () {
    Route::get('', [EventApiController::class, 'index']);
    Route::get('/upcoming', [EventApiController::class, 'upcoming']);
    Route::get('/past', [EventApiController::class, 'past']);
    Route::get('/{id}', [EventApiController::class, 'show']);
});