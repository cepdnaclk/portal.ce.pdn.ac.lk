<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SemesterResource;
use App\Domains\Semester\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SemesterApiController extends Controller
{
    public function index(Request $request)
    {
        Log::debug('Entering CourseApiController@index', ['request' => $request->all()]);
        $query = Semester::where('academic_program', 'Undergraduate');

        if ($request->has('curriculum')) {
            $query->where('version', $request->curriculum);
            Log::debug('Filtering by curriculum', ['curriculum' => $request->curriculum]);
        }

        if ($request->has('semester')) {
            $query->where('id', $request->semester);
            Log::debug('Filtering by semester', ['semester' => $request->semester]);
        }

        $semesters = $query->paginate(20);
        Log::debug('Semesters fetched', ['count' => $semesters->count()]);

        if ($semesters->isEmpty()) {
            Log::info('No semesters found for the given criteria');
            return response()->json([
                'message' => 'Semester not found',                  
            ], 404);
        }

        return SemesterResource::collection($semesters);
    }
}