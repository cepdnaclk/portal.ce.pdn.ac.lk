<?php

use Tabuna\Breadcrumbs\Trail;
use App\Domains\ContentManagement\Models\News;
use App\Http\Controllers\Backend\GalleryNewsController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\NewsController;

Route::group(['middleware' => ['permission:user.access.editor.news', 'tenant.access']], function () {

  Route::get('/news', function () {
    return view('backend.news.index');
  })->name('news.index')
    ->breadcrumbs(function (Trail $trail) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('News'), route('dashboard.news.index'));
    });

  // Create
  Route::get('news/create', [NewsController::class, 'create'])
    ->name('news.create')
    ->breadcrumbs(function (Trail $trail) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('News'), route('dashboard.news.index'))
        ->push(__('Create'));
    });

  // Store
  Route::post('news/', [NewsController::class, 'store'])
    ->name('news.store');

  // Edit
  Route::get('news/edit/{news}', [NewsController::class, 'edit'])
    ->name('news.edit')
    ->breadcrumbs(function (Trail $trail) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('News'), route('dashboard.news.index'))
        ->push(__('Edit'));
    });

  // Update
  Route::put('news/{news}', [NewsController::class, 'update'])
    ->name('news.update');

  // Delete
  Route::get('news/delete/{news}', [NewsController::class, 'delete'])
    ->name('news.delete')
    ->breadcrumbs(function (Trail $trail) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('News'), route('dashboard.news.index'))
        ->push(__('Delete'));
    });

  // Destroy
  Route::delete('news/{news}', [NewsController::class, 'destroy'])
    ->name('news.destroy');

  //Preview
  Route::get('news/preview/{news}', function (News $news) {
    return view('backend.news.preview', compact('news'));
  })->name('news.preview');

  // Gallery management routes
  Route::get('news/{news}/gallery', [GalleryNewsController::class, 'index'])
    ->name('news.gallery.index')
    ->breadcrumbs(function (Trail $trail, $news) {
      $newsModel = News::findOrFail($news);
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('News'), route('dashboard.news.index'))
        ->push(__($newsModel->title), route('dashboard.news.edit', $newsModel))
        ->push(__('Gallery'));
    });

  Route::post('news/{news}/gallery/upload', [GalleryNewsController::class, 'upload'])
    ->name('news.gallery.upload')
    ->middleware('throttle:gallery-uploads');

  Route::put('news/{news}/gallery/{image}/cover', [GalleryNewsController::class, 'setCover'])
    ->name('news.gallery.set-cover');

  Route::post('news/{news}/gallery/reorder', [GalleryNewsController::class, 'reorder'])
    ->name('news.gallery.reorder');
});