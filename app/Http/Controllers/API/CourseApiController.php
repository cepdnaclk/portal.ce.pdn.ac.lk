<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Domains\Course\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourseApiController extends Controller
{
    public function index(Request $request)
    {
        Log::debug('Entering CourseApiController@index', ['request' => $request->all()]);

        $query = Course::where('academic_program', 'Undergraduate');

        if ($request->has('curriculum')) {
            $query->where('version', $request->curriculum);
            Log::debug('Filtering by curriculum', ['curriculum' => $request->curriculum]);
        }

        if ($request->has('semester')) {
            $query->where('semester_id', $request->semester);
            Log::debug('Filtering by semester', ['semester' => $request->semester]);
        }

        if ($request->has('type')) {
            $query->where('type', $request->type);
            Log::debug('Filtering by type', ['type' => $request->type]);
        }

        $courses = $query->paginate(20);
        Log::debug('Courses fetched', ['count' => $courses->count()]);

        if ($courses->isEmpty()) {
            Log::info('No courses found for the given criteria');
            return response()->json([
                'message' => 'Course not found',                  
            ], 404);
        }

        return CourseResource::collection($courses);
    }
}