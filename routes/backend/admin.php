<?php

use App\Http\Controllers\Backend\DashboardController;
use Tabuna\Breadcrumbs\Trail;

// All route names are prefixed with 'admin.'.
Route::redirect('/', '/dashboard/home', 301);

Route::get('home', [DashboardController::class, 'index'])
    ->name('home')
    ->breadcrumbs(function (Trail $trail) {
        $trail->push(__('Home'), route('dashboard.home'));
    });
