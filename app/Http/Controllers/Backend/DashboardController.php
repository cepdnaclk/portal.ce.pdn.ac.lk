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
        try{
            return view('backend.dashboard');
        }catch (\Exception $ex) {       
            Log::error('Failed to load dashboard', ['error' => $ex->getMessage()]);
            return abort(500);
        }
    }
}
