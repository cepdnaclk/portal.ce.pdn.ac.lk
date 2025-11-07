<?php

use Tabuna\Breadcrumbs\Trail;
use App\Domains\ContentManagement\Models\Event;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\EventController;
use App\Http\Controllers\Backend\GalleryEventController;

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
  Route::get('events/preview/{event}', function (Event $event) {
    return view('backend.event.preview', compact('event'));
  })->name('event.preview');

  // Gallery management routes
  Route::get('events/{event}/gallery', [GalleryEventController::class, 'index'])
    ->name('event.gallery.index')
    ->breadcrumbs(function (Trail $trail, $event) {
      $eventModel = Event::findOrFail($event);
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Event'), route('dashboard.event.index'))
        ->push(__($eventModel->title), route('dashboard.event.edit', $eventModel))
        ->push(__('Gallery'));
    });

  Route::post('events/{event}/gallery/upload', [GalleryEventController::class, 'upload'])
    ->name('event.gallery.upload');

  Route::put('events/{event}/gallery/{image}/cover', [GalleryEventController::class, 'setCover'])
    ->name('event.gallery.set-cover');

  Route::post('events/{event}/gallery/reorder', [GalleryEventController::class, 'reorder'])
    ->name('event.gallery.reorder');
});
