<?php

use App\Http\Controllers\Backend\NewsApiController;
use Illuminate\Http\Request;

Route::get('/news',[NewsApiController::class,'index']);
Route::get('/news/{id}',[NewsApiController::class,'show']);