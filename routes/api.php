<?php

use App\Domains\Tenant\Models\Tenant;
use App\Http\Controllers\API\ArticleApiController;
use App\Http\Controllers\API\NewsApiController;
use App\Http\Controllers\API\EventApiController;
use App\Http\Controllers\API\V2\EventApiController as EventApiV2Controller;
use App\Http\Controllers\API\CourseApiController;
use App\Http\Controllers\API\SemesterApiController;
use App\Http\Controllers\API\TaxonomyApiController;
use App\Http\Controllers\API\V2\AnnouncementApiController as AnnouncementApiV2Controller;
use App\Http\Controllers\API\V2\ArticleApiController as ArticleApiV2Controller;
use App\Http\Controllers\API\V2\NewsApiController as NewsApiV2Controller;
use App\Http\Controllers\API\V2\TaxonomyApiController as TaxonomyApiV2Controller;

// V1 API Routes
Route::group(['prefix' => 'news/v1', 'as' => 'api.news.'], function () {
  Route::get('/', [NewsApiController::class, 'index']);
  Route::get('/{id}', [NewsApiController::class, 'show']);
});

Route::group(['prefix' => 'articles/v1', 'as' => 'api.articles.'], function () {
  Route::get('/', [ArticleApiController::class, 'index']);
  Route::get('/{id}', [ArticleApiController::class, 'show']);
  Route::get('/category/{category}', [ArticleApiController::class, 'category']);
});

Route::group(['prefix' => 'events/v1', 'as' => 'api.events.'], function () {
  Route::get('', [EventApiController::class, 'index']);
  Route::get('/upcoming', [EventApiController::class, 'upcoming']);
  Route::get('/past', [EventApiController::class, 'past']);
  Route::get('/{id}', [EventApiController::class, 'show']);
});


Route::group(['prefix' => 'academic/v1/undergraduate', 'as' => 'api.academic.undergraduate.'], function () {
  Route::get('/courses', [CourseApiController::class, 'index']);
  Route::get('/semesters', [SemesterApiController::class, 'index']);
});

// TODO: Implement postgraduate courses API
Route::group(['prefix' => 'taxonomy/v1/', 'as' => 'api.taxonomy.'], function () {
  Route::get('/', [TaxonomyApiController::class, 'index'])->name('index');
  Route::get('/{taxonomy_code}', [TaxonomyApiController::class, 'get_taxonomy'])->name('get');
  Route::get(
    'term/{term_code}',
    [TaxonomyApiController::class, 'get_term']
  )->name('term.get');
});


// V2 API Routes
Route::group(['as' => 'api.v2.'], function () {
  // Tenants Endpoints ------------------------------------
  Route::get('/tenants/v2', function () {
    $tenants = Tenant::all(['name', 'slug', 'url', 'description']);
    return response()->json($tenants);
  })->name('tenants.index');

  // News Endpoints ---------------------------------------
  Route::get('/news/v2', function () {
    // Redirect to default tenant news endpoint
    $defaultTenant = Tenant::default()->slug ?? 'default';
    return redirect()->to("/api/news/v2/{$defaultTenant}");
  })->name('news.default');

  Route::group(['prefix' => 'news/v2/{tenant_slug}', 'as' => 'news.'], function () {
    Route::get('/', [NewsApiV2Controller::class, 'index'])->name('index');
    Route::get('/{id}', [NewsApiV2Controller::class, 'show'])->name('show');
  });

  // Articles Endpoints ---------------------------------------
  Route::get('/articles/v2', function () {
    // Redirect to default tenant articles endpoint
    $defaultTenant = Tenant::default()->slug ?? 'default';
    return redirect()->to("/api/articles/v2/{$defaultTenant}");
  })->name('articles.default');

  Route::group(['prefix' => 'articles/v2/{tenant_slug}', 'as' => 'articles.'], function () {
    Route::get('/', [ArticleApiV2Controller::class, 'index'])->name('index');
    Route::get('/{id}', [ArticleApiV2Controller::class, 'show'])->name('show');
    Route::get('/category/{category}', [ArticleApiV2Controller::class, 'category'])->name('category');
  });

  // Events Endpoints -------------------------------------
  Route::get('/events/v2', function () {
    // Redirect to default tenant events endpoint
    $defaultTenant = Tenant::default()->slug ?? 'default';
    return redirect()->to("/api/events/v2/{$defaultTenant}");
  })->name('events.default');

  Route::group(['prefix' => 'events/v2/{tenant_slug}', 'as' => 'events.'], function () {
    Route::get('', [EventApiV2Controller::class, 'index'])->name('index');
    Route::get('/upcoming', [EventApiV2Controller::class, 'upcoming'])->name('upcoming');
    Route::get('/past', [EventApiV2Controller::class, 'past'])->name('past');
    Route::get('/{id}', [EventApiV2Controller::class, 'show'])->name('show');
  });

  // Announcements Endpoints ------------------------------
  Route::get('/announcements/v2', function () {
    // Redirect to default tenant news endpoint
    $defaultTenant = Tenant::default()->slug ?? 'default';
    return redirect()->to("/api/announcements/v2/{$defaultTenant}");
  })->name('announcements.default');

  Route::group(['prefix' => 'announcements/v2/{tenant_slug}', 'as' => 'announcements.'], function () {
    Route::get('', [AnnouncementApiV2Controller::class, 'index'])->name('index');
  });

  // Taxonomy Endpoints ----------------------------------
  Route::get('/taxonomy/v2', function () {
    $defaultTenant = Tenant::default()->slug ?? 'default';
    return redirect()->to("/api/taxonomy/v2/{$defaultTenant}");
  });

  Route::group(['prefix' => 'taxonomy/v2/{tenant_slug}', 'as' => 'taxonomy.'], function () {
    Route::get('/', [TaxonomyApiV2Controller::class, 'index'])->name('index');
    Route::get('/{taxonomy_code}', [TaxonomyApiV2Controller::class, 'get_taxonomy'])->name('get_taxonomy');
    Route::get('/term/{term_code}', [TaxonomyApiV2Controller::class, 'get_term'])->name('get_term');
  });
});
