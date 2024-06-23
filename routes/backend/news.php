<?php

use Tabuna\Breadcrumbs\Trail;
use App\Http\Controllers\Backend\NewsItemController;

Route::group([], function () {

    Route::get('/news', function () {
        return view('backend.news.index');
    })->name('news.index')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('News'), route('dashboard.news.index'));
        });

    // Create
    Route::get('news/create', [NewsItemController::class, 'create'])
        ->name('news.create')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('News'), route('dashboard.news.index'))
                ->push(__('Create'));
        });

    // Store
    Route::post('news/', [NewsItemController::class, 'store'])
        ->name('news.store');

    // Edit
    Route::get('news/edit/{newsItem}', [NewsItemController::class, 'edit'])
        ->name('news.edit')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('News'), route('dashboard.news.index'))
                ->push(__('Edit'));
        });

    // Update
    Route::put('news/{newsItem}', [NewsItemController::class, 'update'])
        ->name('news.update');

    // Delete
    Route::get('news/delete/{newsItem}', [NewsItemController::class, 'delete'])
        ->name('news.delete')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('News'), route('dashboard.news.index'))
                ->push(__('Delete'));
        });

    // Destroy
    Route::delete('news/{newsItem}', [NewsItemController::class, 'destroy'])
        ->name('news.destroy');
});
