<?php

use Tabuna\Breadcrumbs\Trail;
use App\Http\Controllers\Backend\SemesterController;

Route::group([], function () {

    // Index
    Route::get('/semesters', function () {
        return view('backend.semesters.index');
    })->name('semesters.index')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Academic Program'), route('dashboard.academic_program.index'))
                ->push(__('Semesters'), route('dashboard.semesters.index'));
        });

    // Create
    Route::get('semesters/create', [SemesterController::class, 'create'])
        ->name('semesters.create')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Academic Program'), route('dashboard.academic_program.index'))
                ->push(__('Semesters'), route('dashboard.semesters.index'))
                ->push(__('Create'));
        });

    // Store
    Route::post('semesters', [SemesterController::class, 'store'])
        ->name('semesters.store');

    // Edit
    Route::get('semesters/edit/{semester}', [SemesterController::class, 'edit'])
        ->name('semesters.edit')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Academic Program'), route('dashboard.academic_program.index'))
                ->push(__('Semesters'), route('dashboard.semesters.index'))
                ->push(__('Edit'));
        });

    // Update
    Route::put('semesters/{semester}', [SemesterController::class, 'update'])
        ->name('semesters.update');

    // Delete
    Route::get('semesters/delete/{semester}', [SemesterController::class, 'delete'])
        ->name('semesters.delete')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Academic Program'), route('dashboard.academic_program.index'))
                ->push(__('Semesters'), route('dashboard.semesters.index'))
                ->push(__('Delete'));
        });

    // Destroy
    Route::delete('semesters/{semester}', [SemesterController::class, 'destroy'])
        ->name('semesters.destroy');
});