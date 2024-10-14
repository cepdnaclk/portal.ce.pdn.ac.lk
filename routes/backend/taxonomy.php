<?php

use Tabuna\Breadcrumbs\Trail;
use App\Http\Controllers\Backend\TaxonomyController;
use App\Http\Controllers\Backend\TaxonomyTermController;
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

    //Taxonomy Term Routes
    Route::group(['prefix' => 'taxonomy/{taxonomy}/terms'], function () {

        // Index (list terms for a taxonomy)
        Route::get('/', [TaxonomyTermController::class, 'index'])->name('taxonomy.terms.index')
            ->breadcrumbs(function (Trail $trail, $taxonomy) {
                $trail->push(__('Home'), route('dashboard.home'))
                    ->push(__('Taxonomy'), route('dashboard.taxonomy.index'))
                    ->push($taxonomy->name, route('dashboard.taxonomy.edit', $taxonomy))
                    ->push(__('Terms'), route('dashboard.taxonomy.terms.index', $taxonomy));
            });

        // Create (show form for creating a new term)
        Route::get('/create', [TaxonomyTermController::class, 'create'])
            ->name('taxonomy.terms.create')
            ->breadcrumbs(function (Trail $trail, $taxonomy) {
                $trail->push(__('Home'), route('dashboard.home'))
                    ->push(__('Taxonomy'), route('dashboard.taxonomy.index'))
                    ->push($taxonomy->name, route('dashboard.taxonomy.edit', $taxonomy))
                    ->push(__('Create Term'));
            });

        // Store (store a new term in the taxonomy)
        Route::post('/', [TaxonomyTermController::class, 'store'])
            ->name('taxonomy.terms.store');

        // Edit (show form for editing a term)
        Route::get('/edit/{term}', [TaxonomyTermController::class, 'edit'])
            ->name('taxonomy.terms.edit')
            ->breadcrumbs(function (Trail $trail, $taxonomy, $term) {
                $trail->push(__('Home'), route('dashboard.home'))
                    ->push(__('Taxonomy'), route('dashboard.taxonomy.index'))
                    ->push($taxonomy->name, route('dashboard.taxonomy.edit', $taxonomy))
                    ->push(__('Edit Term'));
            });

        // Update (update a term in the taxonomy)
        Route::put('/{term}', [TaxonomyTermController::class, 'update'])
            ->name('taxonomy.terms.update');

        // Delete (show confirmation for deleting a term)
        Route::get('/delete/{term}', [TaxonomyTermController::class, 'delete'])
            ->name('taxonomy.terms.delete')
            ->breadcrumbs(function (Trail $trail, $taxonomy, $term) {
                $trail->push(__('Home'), route('dashboard.home'))
                    ->push(__('Taxonomy'), route('dashboard.taxonomy.index'))
                    ->push($taxonomy->name, route('dashboard.taxonomy.edit', $taxonomy))
                    ->push(__('Delete Term'));
            });

        // Destroy (delete a term from the taxonomy)
        Route::delete('/{term}', [TaxonomyTermController::class, 'destroy'])
            ->name('taxonomy.terms.destroy');
    });
});
