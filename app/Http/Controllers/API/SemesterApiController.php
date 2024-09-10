<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\SemesterResource;
use App\Domains\Semester\Models\Semester;
use Illuminate\Http\Request;

class SemesterApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Semester::where('academic_program', 'Undergraduate');

        if ($request->has('curriculum')) {
            $query->where('version', $request->curriculum);
        }

        if ($request->has('semester')) {
            $query->where('id', $request->semester);
        }

        $semesters = $query->paginate(20);
        if ($semesters->isEmpty()) {
            return response()->json([
                'message' => 'Semester not found',                  
            ], 404);
        }

        return SemesterResource::collection($semesters);
    }
}