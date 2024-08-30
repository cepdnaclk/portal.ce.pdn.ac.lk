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
            ->push(__('Academic Program'), route('dashboard.academic_program.index'))
                ->push(__('Courses'), route('dashboard.courses.index'));
        });

    // Create
    Route::get('courses/create', function () {
        return view('backend.courses.create');
    })->name('courses.create')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Academic Program'), route('dashboard.academic_program.index'))
                ->push(__('courses'), route('dashboard.courses.index'))
                ->push(__('Create'));
        });
});