<?php

use Tabuna\Breadcrumbs\Trail;
use App\Http\Controllers\Backend\TaxonomyController;
use App\Http\Controllers\Backend\TaxonomyFileController;
use App\Http\Controllers\Backend\TaxonomyTermController;
use Illuminate\Support\Facades\Route;


// Taxonomy Data
Route::group(['middleware' => ['permission:user.taxonomy.data.editor|user.taxonomy.data.viewer']], function () {
    // Index
    Route::get('taxonomy', function () {
        return view('backend.taxonomy.index');
    })->name('taxonomy.index')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Taxonomy'), route('dashboard.taxonomy.index'));
        });


    // View
    Route::get('taxonomy/view/{taxonomy}', [TaxonomyController::class, 'view'])
        ->name('taxonomy.view')->breadcrumbs(function (Trail $trail, $taxonomy) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Taxonomy'), route('dashboard.taxonomy.index'))
                ->push($taxonomy->name)
                ->push(__('View'));
        });

    // Only Editors have access to these functionalities
    Route::group(['middleware' => ['permission:user.taxonomy.data.editor']], function () {

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
                    ->push($taxonomy->name)
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
                    ->push($taxonomy->name)
                    ->push(__('Delete'));
            });
        // Destroy
        Route::delete('taxonomy/{taxonomy}', [TaxonomyController::class, 'destroy'])
            ->name('taxonomy.destroy');
    });

    //Taxonomy Term Routes
    Route::group(['prefix' => 'taxonomy/{taxonomy}/terms'], function () {

        // Index (list terms for a taxonomy)
        Route::get('/', [TaxonomyTermController::class, 'index'])->name('taxonomy.terms.index')
            ->breadcrumbs(function (Trail $trail, $taxonomy) {
                $trail->push(__('Home'), route('dashboard.home'))
                    ->push(__('Taxonomy'), route('dashboard.taxonomy.index'))
                    ->push($taxonomy->name)
                    ->push(__('Terms'), route('dashboard.taxonomy.terms.index', $taxonomy));
            });

        // Only Editors have access to these functionalities
        Route::group(['middleware' => ['permission:user.taxonomy.data.editor']], function () {
            // Create (show form for creating a new term)
            Route::get('/create', [TaxonomyTermController::class, 'create'])
                ->name('taxonomy.terms.create')
                ->breadcrumbs(function (Trail $trail, $taxonomy) {
                    $trail->push(__('Home'), route('dashboard.home'))
                        ->push(__('Taxonomy'), route('dashboard.taxonomy.index'))
                        ->push($taxonomy->name, route('dashboard.taxonomy.edit', $taxonomy))
                        ->push(__('Terms'), route('dashboard.taxonomy.terms.index', $taxonomy))
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
                        ->push(__('Terms'), route('dashboard.taxonomy.terms.index', $taxonomy))
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
                        ->push(__('Terms'), route('dashboard.taxonomy.terms.index', $taxonomy))
                        ->push(__('Delete Term'));
                });

            // Destroy (delete a term from the taxonomy)
            Route::delete('/{term}', [TaxonomyTermController::class, 'destroy'])
                ->name('taxonomy.terms.destroy');
        });
    });
});

// Taxonomy Files
Route::group(['middleware' => ['permission:user.taxonomy.file.editor|user.taxonomy.file.viewer']], function () {
    // Index
    Route::get('taxonomy-files', function () {
        return view('backend.taxonomy_file.index');
    })
        ->name('taxonomy-files.index')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Taxonomy Files'), route('dashboard.taxonomy-files.index'));
        });

    // View
    Route::get('taxonomy-files/view/{taxonomyFile}', [TaxonomyFileController::class, 'view'])
        ->name('taxonomy-files.view')
        ->breadcrumbs(function (Trail $trail, $taxonomyFile) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Taxonomy Files'), route('dashboard.taxonomy-files.index'))
                ->push($taxonomyFile->file_name)
                ->push(__('View'));
        });

    // Download
    Route::get('taxonomy-files/download/{file_name}', [TaxonomyFileController::class, 'download'])
        ->name('taxonomy-files.download');

    // Only Editors have access to these functionalities
    Route::group(['middleware' => ['permission:user.taxonomy.data.editor']], function () {
        // Create form
        Route::get('taxonomy-files/create', [TaxonomyFileController::class, 'create'])
            ->name('taxonomy-files.create')
            ->breadcrumbs(function (Trail $trail) {
                $trail->push(__('Home'), route('dashboard.home'))
                    ->push(__('Taxonomy Files'), route('dashboard.taxonomy-files.index'))
                    ->push(__('Upload'));
            });

        // Store (POST)
        Route::post('taxonomy-files', [TaxonomyFileController::class, 'store'])
            ->name('taxonomy-files.store');

        // Edit form
        Route::get('taxonomy-files/edit/{taxonomyFile}', [TaxonomyFileController::class, 'edit'])
            ->name('taxonomy-files.edit')
            ->breadcrumbs(function (Trail $trail, $taxonomyFile) {
                $trail->push(__('Home'), route('dashboard.home'))
                    ->push(__('Taxonomy Files'), route('dashboard.taxonomy-files.index'))
                    ->push($taxonomyFile->file_name)
                    ->push(__('Edit'), route('dashboard.taxonomy-files.edit', $taxonomyFile));
            });

        // Update (PUT / PATCH)
        Route::put('taxonomy-files/{taxonomyFile}', [TaxonomyFileController::class, 'update'])
            ->name('taxonomy-files.update');

        // Delete confirmation
        Route::get('taxonomy-files/delete/{taxonomyFile}', [TaxonomyFileController::class, 'delete'])
            ->name('taxonomy-files.delete')
            ->breadcrumbs(function (Trail $trail, $taxonomyFile) {
                $trail->push(__('Home'), route('dashboard.home'))
                    ->push(__('Taxonomy Files'), route('dashboard.taxonomy-files.index'))
                    ->push($taxonomyFile->file_name)
                    ->push(__('Delete'));
            });

        // Destroy (DELETE)
        Route::delete('taxonomy-files/{taxonomyFile}', [TaxonomyFileController::class, 'destroy'])
            ->name('taxonomy-files.destroy');
    });
});

// Taxonomy Pages
Route::group(['middleware' => ['permission:user.taxonomy.page.editor|user.taxonomy.page.viewer']], function () {
    // Index
    Route::get('taxonomy-pages', function () {
        return response()->make('Taxonomy Pages not implemented yet.', 200);
    })->name('taxonomy.pages.index')
        ->breadcrumbs(function (Trail $trail) {
            $trail->push(__('Home'), route('dashboard.home'))
                ->push(__('Taxonomy pages'), route('dashboard.taxonomy.page.index'));
        });
});