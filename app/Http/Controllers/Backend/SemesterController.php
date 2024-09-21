<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Domains\Semester\Models\Semester;
use App\Domains\Course\Models\Course;
use Illuminate\Support\Facades\Log;

class SemesterController extends Controller
{
    /**
     * Display a listing of the semesters.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $semesters = Semester::all();
        return view('backend.semesters.index', compact('semesters'));
    }

    /**
     * Show the form for creating a new semester.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('backend.semesters.create');
    }

    /**
     * Store a newly created semester in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'version' => ['required', 'integer', Rule::in(array_keys(Semester::getVersions()))],
            'academic_program' => ['required', Rule::in(array_values(Semester::getAcademicPrograms()))],
            'description' => 'nullable|string',
            'url' => [
                'required',
                'string',
                'unique:semesters', 
        ],
        ]);
        try {
            $validatedData['created_by'] = auth()->user()->id;
            $validatedData['updated_by'] = auth()->user()->id;
            $validatedData['url'] = urlencode(str_replace(" ", "-", $request->url));
            Semester::create($validatedData);
            return redirect()->route('dashboard.semesters.index')->with('success', 'Semester created successfully.');
        } catch (\Exception $e) {
            Log::error('Error in storing semester: '.$e->getMessage());
            return abort(500);
        }
    }

    
    /**
     * Show the form for editing the specified semester.
     *
     * @param  \App\Domains\Semester\Models\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function edit(Semester $semester)
    {
        return view('backend.semesters.edit', compact('semester'));
    }

    /**
     * Update the specified semester in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Domains\Semester\Models\Semester  $semester
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Semester $semester)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'version' => ['required', 'integer', Rule::in(array_keys(Semester::getVersions()))],
            'academic_program' => ['required', Rule::in(array_values(Semester::getAcademicPrograms()))],
            'description' => 'nullable|string',
            'url' => [
                'required',
                'string',
                Rule::unique('semesters', 'url')->ignore($semester->id),
            ],
        ]);
        try {
            $validatedData['updated_by'] = auth()->user()->id;
            $validatedData['url'] = urlencode(str_replace(" ", "-", $request->url));
            $semester->update($validatedData);
            return redirect()->route('dashboard.semesters.index')->with('success', 'Semester updated successfully.');
        } catch (\Exception $e) { 
            Log::error('Error in updating semester: '.$e->getMessage());  
            return abort(500);
        }
    }

    /**
     * Remove the specified semester from storage.
     *
     * @param  \App\Domains\Semester\Models\Semester  $semester
     * @return \Illuminate\Http\Response
     */

     public function delete(Semester $semester)
     {
         $courses = Course::where('semester_id', $semester->id)->get();
     
         if ($courses->count() > 0) {
             return view('backend.semesters.delete', compact('semester', 'courses'));
         }
     
         return view('backend.semesters.delete', compact('semester','courses'));
     }


    public function destroy(Semester $semester)
    {
        
        $courses = Course::where('semester_id', $semester->id)->get();

        if ($courses->count() > 0) {
            return redirect()->route('dashboard.semesters.index')
                ->withErrors('Cannot delete semester as it has associated courses. Please reassign or delete these courses first.');
        }

        
        try{
            $semester->delete();
            return redirect()->route('dashboard.semesters.index')->with('success', 'Semester deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error in deleting semester: '.$e->getMessage());
            return abort(500);
        }
    }
}
