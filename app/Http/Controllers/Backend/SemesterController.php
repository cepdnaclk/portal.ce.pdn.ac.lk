<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Domains\Semester\Models\Semester;

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
            'version' => 'required|integer',
            'academic_program' => ['required', Rule::in(Semester::ACADEMIC_PROGRAMS)],
            'description' => 'nullable|string',
            'url' => 'nullable|string',
        ]);
        try {
            Semester::create($validatedData);
            return redirect()->route('dashboard.semesters.index')->with('success', 'Semester created successfully.');
        } catch (\Exception $e) {
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
            'version' => 'required|integer',
            'academic_program' => 'required|in:Undergraduate,Postgraduate',
            'description' => 'nullable|string',
            'url' => 'nullable|string',
        ]);
        try {
            $semester->update($validatedData);
            return redirect()->route('dashboard.semesters.index')->with('success', 'Semester updated successfully.');
        } catch (\Exception $e) {   
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
        return view('backend.semesters.delete', compact('semester'));
    }

    public function destroy(Semester $semester)
    {
        try{
            $semester->delete();
            return redirect()->route('backend.semesters.index')->with('success', 'Semester deleted successfully.');
        } catch (\Exception $e) {
            return abort(500);
        }
    }
}
