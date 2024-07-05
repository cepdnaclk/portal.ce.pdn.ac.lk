<?php

use App\Http\Controllers\Backend\NewsApiController;
use App\Http\Controllers\Backend\EventApiController;
use Illuminate\Http\Request;

Route::get('/news',[NewsApiController::class,'index']);
Route::get('/news/{id}',[NewsApiController::class,'show']);

Route::get('/event',[EventApiController::class,'index']);
Route::get('/event/{id}',[EventApiController::class,'show']);