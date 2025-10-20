<?php

use Tabuna\Breadcrumbs\Trail;
use App\Domains\Event\Models\Event;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\EventController;

Route::group(['middleware' => ['permission:user.access.editor.events']], function () {

    Route::get('events', function () {
        return view('backend.event.index');
    })->name('event.index')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Event'), route('dashboard.event.index'));
        });

    // Create
    Route::get('events/create', [EventController::class, 'create'])
        ->name('event.create')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Event'), route('dashboard.event.index'))
                ->push(__('Create'));
        });

    // Store
    Route::post('events/', [EventController::class, 'store'])
        ->name('event.store');

    // Edit
    Route::get('events/edit/{event}', [EventController::class, 'edit'])
        ->name('event.edit')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Event'), route('dashboard.event.index'))
                ->push(__('Edit'));
        });


    // Update
    Route::put('events/{event}', [EventController::class, 'update'])
        ->name('event.update');

    // Delete
    Route::get('events/delete/{event}', [EventController::class, 'delete'])
        ->name('event.delete')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Event'), route('dashboard.event.index'))
                ->push(__('Delete'));
        });

    // Destroy
    Route::delete('events/{event}', [EventController::class, 'destroy'])
        ->name('event.destroy');

    //Preview
    Route::get('events/preview/{event}', function (Event $event){
        return view('backend.event.preview', compact('event'));
    })->name('event.preview');

    // Gallery management routes
    Route::get('events/{event}/gallery', [\App\Http\Controllers\Backend\GalleryController::class, 'index'])
        ->name('event.gallery.index')
        ->breadcrumbs(function (Trail $trail, Event $event) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Event'), route('dashboard.event.index'))
                ->push(__('Gallery'));
        });

    Route::post('events/{event}/gallery/upload', [\App\Http\Controllers\Backend\GalleryController::class, 'upload'])
        ->name('event.gallery.upload');

    Route::put('events/{event}/gallery/{image}/cover', [\App\Http\Controllers\Backend\GalleryController::class, 'setCover'])
        ->name('event.gallery.set-cover');

    Route::post('events/{event}/gallery/reorder', [\App\Http\Controllers\Backend\GalleryController::class, 'reorder'])
        ->name('event.gallery.reorder');
});