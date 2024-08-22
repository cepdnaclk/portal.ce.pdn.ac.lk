<?php

use Tabuna\Breadcrumbs\Trail;
use App\Http\Controllers\Backend\AnnouncementController;
use App\Http\Controllers\Backend\CourseController;

Route::group([], function () {

    Route::get('/courses', function () {
        return view('backend.courses.index');
    })->name('courses.index')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Courses'), route('dashboard.courses.index'));
        });

    // Create
    Route::get('courses/create', [CourseController::class, 'create'])
        ->name('courses.create')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Courses'), route('dashboard.courses.index'))
                ->push(__('Create'));
        });

    // Store
    Route::post('courses/', [AnnouncementController::class, 'store'])
        ->name('courses.store');

    // Edit
    Route::get('courses/edit/{announcement}', [AnnouncementController::class, 'edit'])
        ->name('courses.edit')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Courses'), route('dashboard.courses.index'))
                ->push(__('Edit'));
        });

    // Update
    Route::put('courses/{announcement}', [AnnouncementController::class, 'update'])
        ->name('courses.update');

    // Delete
    Route::get('courses/delete/{announcement}', [AnnouncementController::class, 'delete'])
        ->name('courses.delete')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Courses'), route('dashboard.courses.index'))
                ->push(__('Delete'));
        });

    // Destroy
    Route::delete('courses/{announcement}', [AnnouncementController::class, 'destroy'])
        ->name('courses.destroy');
});
