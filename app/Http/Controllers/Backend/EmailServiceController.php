<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailServiceController extends Controller
{
  public function history(Request $request)
  {
    return view('backend.portal-apps.email-service.index');
  }
}
