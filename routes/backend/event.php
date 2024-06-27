<?php

use Tabuna\Breadcrumbs\Trail;
use App\Http\Controllers\Backend\EventItemController;

Route::group([], function () {

    Route::get('/event', function () {
        return view('backend.event.index');
    })->name('event.index')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('event'), route('dashboard.event.index'));
        });

    // Create
    Route::get('event/create', [EventItemController::class, 'create'])
        ->name('event.create')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('event'), route('dashboard.event.index'))
                ->push(__('Create'));
        });

    // Store
    Route::post('event/', [EventItemController::class, 'store'])
        ->name('event.store');

    // Edit
    Route::get('event/edit/{eventItem}', [EventItemController::class, 'edit'])
        ->name('event.edit')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('event'), route('dashboard.event.index'))
                ->push(__('Edit'));
        });

    // Update
    Route::put('event/{eventItem}', [EventItemController::class, 'update'])
        ->name('event.update');

    // Delete
    Route::get('event/delete/{eventItem}', [EventItemController::class, 'delete'])
        ->name('event.delete')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('event'), route('dashboard.event.index'))
                ->push(__('Delete'));
        });

    // Destroy
    Route::delete('event/{eventItem}', [EventItemController::class, 'destroy'])
        ->name('event.destroy');
});
