<?php

namespace App\Http\Controllers\Backend;
use App\Domains\Announcement\Models\Announcement;
use App\Http\Controllers\Controller;


use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function create()
    {
        return view('backend.courses.create');
    }

}