<?php

use App\Domains\ContentManagement\Models\Article;
use App\Http\Controllers\Backend\ArticleContentImageController;
use App\Http\Controllers\Backend\ArticleController;
use App\Http\Controllers\Backend\GalleryArticleController;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

Route::group(['middleware' => ['permission:user.access.editor.articles', 'tenant.access']], function () {

  Route::get('/articles', function () {
    return view('backend.article.index');
  })->name('article.index')
    ->breadcrumbs(function (Trail $trail) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Articles'), route('dashboard.article.index'));
    });

  // Create
  Route::get('articles/create', [ArticleController::class, 'create'])
    ->name('article.create')
    ->breadcrumbs(function (Trail $trail) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Articles'), route('dashboard.article.index'))
        ->push(__('Create'));
    });

  // Store
  Route::post('articles/', [ArticleController::class, 'store'])
    ->name('article.store');

  // Edit
  Route::get('articles/edit/{article}', [ArticleController::class, 'edit'])
    ->name('article.edit')
    ->breadcrumbs(function (Trail $trail) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Articles'), route('dashboard.article.index'))
        ->push(__('Edit'));
    });

  // Update
  Route::put('articles/{article}', [ArticleController::class, 'update'])
    ->name('article.update');

  // Delete
  Route::get('articles/delete/{article}', [ArticleController::class, 'delete'])
    ->name('article.delete')
    ->breadcrumbs(function (Trail $trail) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Articles'), route('dashboard.article.index'))
        ->push(__('Delete'));
    });

  // Destroy
  Route::delete('articles/{article}', [ArticleController::class, 'destroy'])
    ->name('article.destroy');

  // Preview
  Route::get('articles/preview/{article}', function (Article $article) {
    return view('backend.article.preview', compact('article'));
  })->name('article.preview');

  // Content image uploads
  Route::post('articles/content-images/upload', [ArticleContentImageController::class, 'upload'])
    ->name('article.content-images.upload')
    ->middleware('throttle:gallery-uploads');

  // Gallery management routes
  Route::get('articles/{article}/gallery', [GalleryArticleController::class, 'index'])
    ->name('article.gallery.index')
    ->breadcrumbs(function (Trail $trail, $article) {
      $articleModel = Article::findOrFail($article);
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Articles'), route('dashboard.article.index'))
        ->push(__($articleModel->title), route('dashboard.article.edit', $articleModel))
        ->push(__('Gallery'));
    });

  Route::post('articles/{article}/gallery/upload', [GalleryArticleController::class, 'upload'])
    ->name('article.gallery.upload')
    ->middleware('throttle:gallery-uploads');

  Route::put('articles/{article}/gallery/{image}/cover', [GalleryArticleController::class, 'setCover'])
    ->name('article.gallery.set-cover');

  Route::post('articles/{article}/gallery/reorder', [GalleryArticleController::class, 'reorder'])
    ->name('article.gallery.reorder');
});