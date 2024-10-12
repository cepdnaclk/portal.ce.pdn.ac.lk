<?php

use Tabuna\Breadcrumbs\Trail;
use App\Http\Controllers\Backend\TaxonomyController;
use Illuminate\Support\Facades\Route;

Route::group([], function () {

    // Index
    Route::get('taxonomy', function () {
        return view('backend.taxonomy.index');
    })->name('taxonomy.index')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Taxonomy'), route('dashboard.taxonomy.index'));
        });

    // Create
    Route::get('taxonomy/create', [TaxonomyController::class, 'create'])
        ->name('taxonomy.create')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Taxonomy'), route('dashboard.taxonomy.index'))
                ->push(__('Create'));
        });

    // Store
    Route::post('taxonomy', [TaxonomyController::class, 'store'])
        ->name('taxonomy.store');

    // Edit
    Route::get('taxonomy/edit/{taxonomy}', [TaxonomyController::class, 'edit'])
        ->name('taxonomy.edit')
        ->breadcrumbs(function (Trail $trail, $taxonomy) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Taxonomy'), route('dashboard.taxonomy.index'))
                ->push(__('Edit'), route('dashboard.taxonomy.edit', $taxonomy));
        });

    // Update
    Route::put('taxonomy/{taxonomy}', [TaxonomyController::class, 'update'])
        ->name('taxonomy.update');

    // Delete 
    Route::get('taxonomy/delete/{taxonomy}', [TaxonomyController::class, 'delete'])
        ->name('taxonomy.delete')
        ->breadcrumbs(function (Trail $trail, $taxonomy) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Taxonomy'), route('dashboard.taxonomy.index'))
                ->push(__('Delete'));
        });

    // Destroy
    Route::delete('taxonomy/{taxonomy}', [TaxonomyController::class, 'destroy'])
        ->name('taxonomy.destroy');
});
