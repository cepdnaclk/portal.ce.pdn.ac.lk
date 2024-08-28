<?php

namespace App\Http\Controllers\Backend;
use App\Http\Controllers\Controller;


class CourseController extends Controller
{
    public function create()
    {
        return view('backend.courses.create');
    }

}
