<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use App\Domains\AcademicProgram\Semester\Models\Semester;
use App\Domains\AcademicProgram\Course\Models\Course;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class SemesterController extends Controller
{
  /**
   * Display a listing of the semesters.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    try {
      $semesters = Semester::all();
      return view('backend.academics.semesters.index', compact('semesters'));
    } catch (\Exception $e) {
      Log::error('Error fetching semesters: ' . $e->getMessage());
      return abort(500);
    }
  }

  /**
   * Show the form for creating a new semester.
   *
   * @return \Illuminate\Http\Response
   */
  public function create()
  {
    try {
      return view('backend.academics.semesters.create');
    } catch (\Exception $e) {
      Log::error('Error loading semester creation page: ' . $e->getMessage());
      return abort(500);
    }
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
      'academic_program' => ['required', Rule::in(array_keys(Semester::getAcademicPrograms()))],
      'description' => 'nullable|string',
      'url' => [
        'required',
        'string',
        'unique:semesters',
      ],
    ]);

    try {
      $semester = new Semester($validatedData);
      $semester->created_by = Auth::user()->id;
      $semester->updated_by = Auth::user()->id;
      $semester->url = urlencode(str_replace(" ", "-", $request->url));
      $semester->save();

      return redirect()->route('dashboard.semesters.index')->with('success', 'Semester created successfully.');
    } catch (\Exception $e) {
      Log::error('Error in storing semester: ' . $e->getMessage());
      return abort(500);
    }
  }

  /**
   * Show the form for editing the specified semester.
   *
   * @param  \App\Domains\AcademicProgram\Semester\Models\Semester  $semester
   * @return \Illuminate\Http\Response
   */
  public function edit(Semester $semester)
  {
    try {
      return view('backend.academics.semesters.edit', compact('semester'));
    } catch (\Exception $e) {
      Log::error('Error loading semester edit page: ' . $e->getMessage());
      return abort(500);
    }
  }

  /**
   * Update the specified semester in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \App\Domains\AcademicProgram\Semester\Models\Semester  $semester
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, Semester $semester)
  {
    $validatedData = $request->validate([
      'title' => 'required|string|max:255',
      'version' => ['required', 'integer', Rule::in(array_keys(Semester::getVersions()))],
      'academic_program' => ['required', Rule::in(array_keys(Semester::getAcademicPrograms()))],
      'description' => 'nullable|string',
      'url' => [
        'required',
        'string',
        Rule::unique('semesters', 'url')->ignore($semester->id),
      ],
    ]);

    try {
      $semester->update($validatedData);
      $semester->updated_by = Auth::user()->id;
      $semester->url =  urlencode(str_replace(" ", "-", $request->url));
      $semester->save();

      return redirect()->route('dashboard.semesters.index')->with('success', 'Semester updated successfully.');
    } catch (\Exception $e) {
      Log::error('Error in updating semester: ' . $e->getMessage());
      return abort(500);
    }
  }

  /**
   * Remove the specified semester from storage.
   *
   * @param  \App\Domains\AcademicProgram\Semester\Models\Semester  $semester
   * @return \Illuminate\Http\Response
   */
  public function delete(Semester $semester)
  {
    $courses = Course::where('semester_id', $semester->id)->get();
    return view('backend.academics.semesters.delete', compact('semester', 'courses'));
  }


  public function destroy(Semester $semester)
  {
    $courses = Course::where('semester_id', $semester->id)->get();

    if ($courses->count() > 0) {
      return redirect()->route('dashboard.semesters.index')
        ->withErrors('Can not delete the semester as it already has associated courses. Please reassign or delete those courses first.');
    }

    try {
      $semester->delete();
      return redirect()->route('dashboard.semesters.index')->with('success', 'Semester deleted successfully.');
    } catch (\Exception $e) {
      Log::error('Error in deleting semester: ' . $e->getMessage());
      return abort(500);
    }
  }
}