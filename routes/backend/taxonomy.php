<?php

use Tabuna\Breadcrumbs\Trail;
use App\Http\Controllers\Backend\TaxonomyController;
use App\Http\Controllers\Backend\TaxonomyFileController;
use App\Http\Controllers\Backend\TaxonomyPageController;
use App\Http\Controllers\Backend\TaxonomyListController;
use App\Http\Controllers\Backend\TaxonomyTermController;
use Illuminate\Support\Facades\Route;


// Taxonomy Data
Route::group(['middleware' => ['permission:user.access.taxonomy.data.editor|user.access.taxonomy.data.viewer', 'tenant.access']], function () {
  // Index
  Route::get('taxonomy', function () {
    return view('backend.taxonomy.index');
  })->name('taxonomy.index')
    ->breadcrumbs(function (Trail $trail) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Taxonomy'), route('dashboard.taxonomy.index'));
    });


  // Alias URL to redirect into the taxonomy page by taxonomy code
  Route::get('taxonomy/alias/{code}', [TaxonomyController::class, 'alias'])
    ->name('taxonomy.alias');

  // Alias URL to redirect into the term page by taxonomy term code
  Route::get('taxonomy/alias/term/{code}', [TaxonomyTermController::class, 'alias'])
    ->name('taxonomy.term.alias');


  // View
  Route::get('taxonomy/view/{taxonomy}', [TaxonomyController::class, 'view'])
    ->name('taxonomy.view')->breadcrumbs(function (Trail $trail, $taxonomy) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Taxonomy'), route('dashboard.taxonomy.index'))
        ->push($taxonomy->name)
        ->push(__('View'));
    });

  // History
  Route::get('taxonomy/history/{taxonomy}', [TaxonomyController::class, 'history'])
    ->name('taxonomy.history')
    ->breadcrumbs(function (Trail $trail, $taxonomy) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Taxonomy'), route('dashboard.taxonomy.index'))
        ->push($taxonomy->name)
        ->push(__('History'));
    });

  // Only Editors have access to these functionalities
  Route::group(['middleware' => ['permission:user.access.taxonomy.data.editor']], function () {

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

    // History
    Route::get('/history/{term}', [TaxonomyTermController::class, 'history'])
      ->name('taxonomy.terms.history')
      ->breadcrumbs(function (Trail $trail, $taxonomy, $term) {
        $trail->push(__('Home'), route('dashboard.home'))
          ->push(__('Taxonomy'), route('dashboard.taxonomy.index'))
          ->push($taxonomy->name, route('dashboard.taxonomy.terms.index', $taxonomy))
          ->push($term->name)
          ->push(__('History'));
      });

    // Only Editors have access to these functionalities
    Route::group(['middleware' => ['permission:user.access.taxonomy.data.editor']], function () {
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
Route::group(['middleware' => ['permission:user.access.taxonomy.file.editor|user.access.taxonomy.file.viewer', 'tenant.access']], function () {
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


  // Only Editors have access to these functionalities
  Route::group(['middleware' => ['permission:user.access.taxonomy.file.editor']], function () {
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

// Taxonomy Lists
Route::group(['middleware' => ['permission:user.access.taxonomy.list.editor|user.access.taxonomy.list.viewer', 'tenant.access']], function () {
  Route::get('taxonomy-lists', function () {
    return view('backend.taxonomy_list.index');
  })
    ->name('taxonomy-lists.index')
    ->breadcrumbs(function (Trail $trail) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Taxonomy Lists'), route('dashboard.taxonomy-lists.index'));
    });

  Route::get('taxonomy-lists/view/{taxonomyList}', [TaxonomyListController::class, 'view'])
    ->name('taxonomy-lists.view')
    ->breadcrumbs(function (Trail $trail, $taxonomyList) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Taxonomy Lists'), route('dashboard.taxonomy-lists.index'))
        ->push($taxonomyList->name)
        ->push(__('View'));
    });

  Route::get('taxonomy-lists/history/{taxonomyList}', [TaxonomyListController::class, 'history'])
    ->name('taxonomy-lists.history')
    ->breadcrumbs(function (Trail $trail, $taxonomyList) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Taxonomy Lists'), route('dashboard.taxonomy-lists.index'))
        ->push($taxonomyList->name)
        ->push(__('History'));
    });

  Route::group(['middleware' => ['permission:user.access.taxonomy.list.editor']], function () {
    Route::get('taxonomy-lists/create', [TaxonomyListController::class, 'create'])
      ->name('taxonomy-lists.create')
      ->breadcrumbs(function (Trail $trail) {
        $trail->push(__('Home'), route('dashboard.home'))
          ->push(__('Taxonomy Lists'), route('dashboard.taxonomy-lists.index'))
          ->push(__('Create'));
      });

    Route::post('taxonomy-lists', [TaxonomyListController::class, 'store'])
      ->name('taxonomy-lists.store');

    Route::get('taxonomy-lists/edit/{taxonomyList}', [TaxonomyListController::class, 'edit'])
      ->name('taxonomy-lists.edit')
      ->breadcrumbs(function (Trail $trail, $taxonomyList) {
        $trail->push(__('Home'), route('dashboard.home'))
          ->push(__('Taxonomy Lists'), route('dashboard.taxonomy-lists.index'))
          ->push($taxonomyList->name)
          ->push(__('Edit'), route('dashboard.taxonomy-lists.edit', $taxonomyList));
      });
    Route::put('taxonomy-lists/manage/{taxonomyList}', [TaxonomyListController::class, 'update'])
      ->name('taxonomy-lists.update');

    // Manage Items
    Route::get('taxonomy-lists/manage/{taxonomyList}', [TaxonomyListController::class, 'manage'])
      ->name('taxonomy-lists.manage')
      ->breadcrumbs(function (Trail $trail, $taxonomyList) {
        $trail->push(__('Home'), route('dashboard.home'))
          ->push(__('Taxonomy Lists'), route('dashboard.taxonomy-lists.index'))
          ->push($taxonomyList->name, route('dashboard.taxonomy-lists.edit', $taxonomyList))
          ->push(__('Manage'), route('dashboard.taxonomy-lists.manage', $taxonomyList));
      });
    Route::put('taxonomy-lists/{taxonomyList}', [TaxonomyListController::class, 'update_list'])
      ->name('taxonomy-lists.update_list');

    Route::get('taxonomy-lists/delete/{taxonomyList}', [TaxonomyListController::class, 'delete'])
      ->name('taxonomy-lists.delete')
      ->breadcrumbs(function (Trail $trail, $taxonomyList) {
        $trail->push(__('Home'), route('dashboard.home'))
          ->push(__('Taxonomy Lists'), route('dashboard.taxonomy-lists.index'))
          ->push($taxonomyList->name)
          ->push(__('Delete'));
      });

    Route::delete('taxonomy-lists/{taxonomyList}', [TaxonomyListController::class, 'destroy'])
      ->name('taxonomy-lists.destroy');
  });
});

// Taxonomy Pages
Route::group(['middleware' => ['permission:user.access.taxonomy.page.editor|user.access.taxonomy.page.viewer', 'tenant.access']], function () {
  // Index
  Route::get('taxonomy-pages', function () {
    return view('backend.taxonomy_page.index');
  })
    ->name('taxonomy-pages.index')
    ->breadcrumbs(function (Trail $trail) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Taxonomy Pages'), route('dashboard.taxonomy-pages.index'));
    });

  // View
  Route::get('taxonomy-pages/view/{taxonomyPage}', [TaxonomyPageController::class, 'view'])
    ->name('taxonomy-pages.view')
    ->breadcrumbs(function (Trail $trail, $taxonomyFile) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Taxonomy Pages'), route('dashboard.taxonomy-pages.index'))
        ->push($taxonomyFile->file_name)
        ->push(__('View'));
    });

  // History
  Route::get('taxonomy-pages/history/{taxonomyPage}', [TaxonomyPageController::class, 'history'])
    ->name('taxonomy-pages.history')
    ->breadcrumbs(function (Trail $trail, $taxonomyPage) {
      $trail->push(__('Home'), route('dashboard.home'))
        ->push(__('Taxonomy Pages'), route('dashboard.taxonomy-pages.index'))
        ->push($taxonomyPage->slug)
        ->push(__('History'));
    });


  // Only Editors have access to these functionalities
  Route::group(['middleware' => ['permission:user.access.taxonomy.page.editor']], function () {
    // Create form
    Route::get('taxonomy-pages/create', [TaxonomyPageController::class, 'create'])
      ->name('taxonomy-pages.create')
      ->breadcrumbs(function (Trail $trail) {
        $trail->push(__('Home'), route('dashboard.home'))
          ->push(__('Taxonomy Pages'), route('dashboard.taxonomy-pages.index'))
          ->push(__('Create'));
      });

    // Store (POST)
    Route::post('taxonomy-pages', [TaxonomyPageController::class, 'store'])
      ->name('taxonomy-pages.store');

    // Edit form
    Route::get('taxonomy-pages/edit/{taxonomyPage}', [TaxonomyPageController::class, 'edit'])
      ->name('taxonomy-pages.edit')
      ->breadcrumbs(function (Trail $trail, $taxonomyPage) {
        $trail->push(__('Home'), route('dashboard.home'))
          ->push(__('Taxonomy Pages'), route('dashboard.taxonomy-pages.index'))
          ->push($taxonomyPage->slug)
          ->push(__('Edit'), route('dashboard.taxonomy-pages.edit', $taxonomyPage));
      });

    // Update (PUT / PATCH)
    Route::put('taxonomy-pages/{taxonomyPage}', [TaxonomyPageController::class, 'update'])
      ->name('taxonomy-pages.update');

    // Delete confirmation
    Route::get('taxonomy-pages/delete/{taxonomyPage}', [TaxonomyPageController::class, 'delete'])
      ->name('taxonomy-pages.delete')
      ->breadcrumbs(function (Trail $trail, $taxonomyPage) {
        $trail->push(__('Home'), route('dashboard.home'))
          ->push(__('Taxonomy Pages'), route('dashboard.taxonomy-pages.index'))
          ->push($taxonomyPage->slug)
          ->push(__('Delete'));
      });

    // Destroy (DELETE)
    Route::delete('taxonomy-pages/{taxonomyPage}', [TaxonomyPageController::class, 'destroy'])
      ->name('taxonomy-pages.destroy');
  });
});
