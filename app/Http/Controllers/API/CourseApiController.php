<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Domains\AcademicProgram\Course\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourseApiController extends Controller
{
  public function index(Request $request)
  {
    try {
      $query = Course::with('modules')->where('academic_program', 'Undergraduate');

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

      return CourseResource::collection($courses);
    } catch (\Exception $e) {
      Log::error('Error in CourseApiController@index', ['error' => $e->getMessage()]);
      return response()->json(['message' => 'An error occurred while fetching courses'], 500);
    }
  }
}