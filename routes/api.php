<?php

use App\Domains\Tenant\Models\Tenant;
use App\Http\Controllers\API\NewsApiController;
use App\Http\Controllers\API\EventApiController;
use App\Http\Controllers\API\V2\EventApiController as EventApiV2Controller;
use App\Http\Controllers\API\CourseApiController;
use App\Http\Controllers\API\SemesterApiController;
use App\Http\Controllers\API\TaxonomyApiController;
use App\Http\Controllers\API\V2\AnnouncementApiController as AnnouncementApiV2Controller;
use App\Http\Controllers\API\V2\NewsApiController as NewsApiV2Controller;

// V1 API Routes
Route::group(['prefix' => 'news/v1', 'as' => 'api.news.'], function () {
  Route::get('/', [NewsApiController::class, 'index']);
  Route::get('/{id}', [NewsApiController::class, 'show']);
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
  });

  // News Endpoints ---------------------------------------
  Route::get('/news/v2', function () {
    // Redirect to default tenant news endpoint
    $defaultTenant = Tenant::default()->slug ?? 'default';
    return redirect()->to("/api/news/v2/{$defaultTenant}");
  });

  Route::group(['prefix' => 'news/v2/{tenant_slug}', 'as' => 'news.'], function () {
    Route::get('/', [NewsApiV2Controller::class, 'index']);
    Route::get('/{id}', [NewsApiV2Controller::class, 'show']);
  });

  // Events Endpoints -------------------------------------
  Route::get('/events/v2', function () {
    // Redirect to default tenant events endpoint
    $defaultTenant = Tenant::default()->slug ?? 'default';
    return redirect()->to("/api/events/v2/{$defaultTenant}");
  });

  Route::group(['prefix' => 'events/v2/{tenant_slug}', 'as' => 'events.'], function () {
    Route::get('', [EventApiV2Controller::class, 'index']);
    Route::get('/upcoming', [EventApiV2Controller::class, 'upcoming']);
    Route::get('/past', [EventApiV2Controller::class, 'past']);
    Route::get('/{id}', [EventApiV2Controller::class, 'show']);
  });

  // Announcements Endpoints ------------------------------
  Route::get('/announcements/v2', function () {
    // Redirect to default tenant news endpoint
    $defaultTenant = Tenant::default()->slug ?? 'default';
    return redirect()->to("/api/announcements/v2/{$defaultTenant}");
  });

  Route::group(['prefix' => 'announcements/v2/{tenant_slug}', 'as' => 'announcements.'], function () {
    Route::get('', [AnnouncementApiV2Controller::class, 'index']);
  });
});
