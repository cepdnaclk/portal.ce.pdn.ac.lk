<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Domains\Course\Models\Course;
use Illuminate\Http\Request;

class CourseApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::where('academic_program', 'Undergraduate');

        if ($request->has('curriculum')) {
            $query->where('version', $request->curriculum);
        }

        if ($request->has('semester')) {
            $query->where('semester_id', $request->semester);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
        }

        $courses = $query->paginate(20);
        if ($courses->isEmpty()) {
            return response()->json([
                'message' => 'Course not found',                  
            ], 404);
        }

        return CourseResource::collection($courses);
    }
}