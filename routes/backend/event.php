<?php

use Tabuna\Breadcrumbs\Trail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\EventController;

Route::group(['middleware' => ['permission:admin.access.events.edit']], function () {

    Route::get('/event', function () {
        return view('backend.event.index');
    })->name('event.index')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('event'), route('dashboard.event.index'));
        });

    // Create
    Route::get('event/create', [EventController::class, 'create'])
        ->name('event.create')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('event'), route('dashboard.event.index'))
                ->push(__('Create'));
        });

    // Store
    Route::post('event/', [EventController::class, 'store'])
        ->name('event.store');

    // Edit
    Route::get('event/edit/{event}', [EventController::class, 'edit'])
        ->name('event.edit')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('event'), route('dashboard.event.index'))
                ->push(__('Edit'));
        });


    // Update
    Route::put('event/{event}', [EventController::class, 'update'])
        ->name('event.update');

    // Delete
    Route::get('event/delete/{event}', [EventController::class, 'delete'])
        ->name('event.delete')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('event'), route('dashboard.event.index'))
                ->push(__('Delete'));
        });

    // Destroy
    Route::delete('event/{event}', [EventController::class, 'destroy'])
        ->name('event.destroy');
});
