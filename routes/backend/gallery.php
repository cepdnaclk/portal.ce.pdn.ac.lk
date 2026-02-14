<?php

use App\Http\Controllers\Backend\GalleryController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['permission:user.access.editor.news|user.access.editor.events|user.access.editor.articles', 'tenant.access']], function () {
  Route::put('gallery/{image}', [GalleryController::class, 'update'])
    ->name('gallery.update');

  Route::delete('gallery/{image}', [GalleryController::class, 'destroy'])
    ->name('gallery.destroy');
});