<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Support\Facades\Log;

/**
 * Class DashboardController.
 */
class DashboardController
{
    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        Log::debug('Entering DashboardController@index');
        return view('backend.dashboard');
    }
}
