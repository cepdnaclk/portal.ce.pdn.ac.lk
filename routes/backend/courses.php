<?php

use Tabuna\Breadcrumbs\Trail;
use App\Http\Controllers\Backend\CourseController;

Route::group([], function () {

    // Index
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
    Route::post('courses/', [CourseController::class, 'store'])
        ->name('courses.store');

    // Edit
    Route::get('courses/edit/{course}', [CourseController::class, 'edit'])
        ->name('courses.edit')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Courses'), route('dashboard.courses.index'))
                ->push(__('Edit'));
        });

    // Update
    Route::put('courses/{course}', [CourseController::class, 'update'])
        ->name('courses.update');

    // Delete
    Route::get('courses/delete/{course}', [CourseController::class, 'delete'])
        ->name('courses.delete')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Courses'), route('dashboard.courses.index'))
                ->push(__('Delete'));
        });

    // Destroy
    Route::delete('courses/{announcement}', [CourseController::class, 'destroy'])
        ->name('courses.destroy');
});