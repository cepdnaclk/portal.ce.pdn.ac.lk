<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Domains\Semester\Models\Semester;
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
        Log::debug('Entering SemesterController@index');
        $semesters = Semester::all();
        Log::debug('Fetched all semesters', ['count' => $semesters->count()]);
        return view('backend.semesters.index', compact('semesters'));
    }

    /**
     * Show the form for creating a new semester.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Log::debug('Entering SemesterController@create');
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
        Log::debug('Entering SemesterController@store', ['request' => $request->all()]);
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
        Log::debug('Validated data', ['data' => $validatedData]);
        try {
            $validatedData['created_by'] = auth()->user()->id;
            $validatedData['updated_by'] = auth()->user()->id;
            $validatedData['url'] = urlencode(str_replace(" ", "-", $request->url));
            $semester = Semester::create($validatedData);
            Log::info('Semester created successfully', ['semester_id' => $semester->id]);
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
        Log::debug('Entering SemesterController@edit', ['semester_id' => $semester->id]);
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
        Log::debug('Entering SemesterController@update', ['semester_id' => $semester->id, 'request' => $request->all()]);
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
        Log::debug('Validated data', ['data' => $validatedData]);
        try {
            $validatedData['updated_by'] = auth()->user()->id;
            $validatedData['url'] = urlencode(str_replace(" ", "-", $request->url));
            $semester->update($validatedData);
            Log::info('Semester updated successfully', ['semester_id' => $semester->id]);
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
        Log::debug('Entering SemesterController@delete', ['semester_id' => $semester->id]);
        return view('backend.semesters.delete', compact('semester'));
    }

    public function destroy(Semester $semester)
    {
        Log::debug('Entering SemesterController@destroy', ['semester_id' => $semester->id]);
        try{
            $semester->delete();
            Log::info('Semester deleted successfully', ['semester_id' => $semester->id]);
            return redirect()->route('dashboard.semesters.index')->with('success', 'Semester deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error in deleting semester: '.$e->getMessage());
            return abort(500);
        }
    }
}
