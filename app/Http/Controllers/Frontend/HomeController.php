<?php

namespace App\Http\Controllers\Frontend;

use App\Services\DepartmentDataService;

/**
 * Class HomeController.
 */
class HomeController
{
  /**
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
   */
  public function index()
  {
    return view('frontend.index');
  }

  /**
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
   */
  public function terms()
  {
    return view('frontend.pages.terms');
  }

  /**
   * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
   */
  public function contributors()
  {
    $projects = [
      '/projects/v1/co225/E20/Computer-Engineering-Portal/',
      '/projects/v1/co227/E20/Computer-Engineering-Portal/',
    ];

    $data = [];
    $api = new DepartmentDataService();

    foreach ($projects as  $key => $url) {
      $projectData = collect($api->getProjectData($url));
      array_push($data, $projectData);
    }

    return view('frontend.pages.contributors', compact('data'));
  }
}