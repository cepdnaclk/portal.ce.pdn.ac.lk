<?php

use App\Domains\Tenant\Http\Controllers\Backend\TenantController;
use App\Domains\Tenant\Models\Tenant;
use Illuminate\Support\Facades\Route;
use Tabuna\Breadcrumbs\Trail;

Route::group([
  'prefix' => 'tenants',
  'as' => 'tenants.',
  'middleware' => 'role:' . config('boilerplate.access.role.admin'),
], function () {
  Route::get('/', [TenantController::class, 'index'])
    ->name('index')
    ->breadcrumbs(function (Trail $trail) {
      $trail->parent('dashboard.home')
        ->push(__('Tenant Management'), route('dashboard.tenants.index'));
    });

  Route::get('create', [TenantController::class, 'create'])
    ->name('create')
    ->breadcrumbs(function (Trail $trail) {
      $trail->parent('dashboard.tenants.index')
        ->push(__('Create Tenant'), route('dashboard.tenants.create'));
    });

  Route::post('/', [TenantController::class, 'store'])->name('store');

  Route::group(['prefix' => '{tenant}'], function () {
    Route::get('edit', [TenantController::class, 'edit'])
      ->name('edit')
      ->breadcrumbs(function (Trail $trail, Tenant $tenant) {
        $trail->parent('dashboard.tenants.index')
          ->push(__('Editing :tenant', ['tenant' => $tenant->name]), route('dashboard.tenants.edit', $tenant));
      });

    Route::patch('/', [TenantController::class, 'update'])->name('update');
    Route::delete('/', [TenantController::class, 'destroy'])->name('destroy');
  });
});