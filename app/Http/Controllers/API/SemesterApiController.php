<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SemesterResource;
use App\Domains\AcademicProgram\Semester\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SemesterApiController extends Controller
{
    public function index(Request $request)
    {
        try {
            $query = Semester::where('academic_program', 'Undergraduate');

            if ($request->has('curriculum')) {
                $query->where('version', $request->curriculum);
            }

            if ($request->has('semester')) {
                $query->where('id', $request->semester);
            }

            $semesters = $query->paginate(20);

            return SemesterResource::collection($semesters);
        } catch (\Exception $e) {
            Log::error('Error in SemesterApiController@index', ['error' => $e->getMessage()]);
            return response()->json(['message' => 'An error occurred while fetching semesters'], 500);
        }
    }
}
