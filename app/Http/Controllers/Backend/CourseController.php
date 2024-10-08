<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Domains\AcademicProgram\Course\Models\Course;
use Illuminate\Support\Facades\Log;

class CourseController extends Controller
{
    /**
     * Display a listing of the courses.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        try {
            $courses = Course::all();
            return view('backend.courses.index', compact('courses'));
        } catch (\Exception $e) {
            Log::error('Error fetching courses: ' . $e->getMessage());
            return abort(500);
        }
    }

    /**
     * Show the form for creating a new course.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        try {
            return view('backend.courses.create');
        } catch (\Exception $e) {
            Log::error('Error loading course creation page: ' . $e->getMessage());
            return abort(500);
        }
    }

    /**
     * Store a newly created course in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:16|unique:courses,code',
            'semester_id' => 'required|integer|exists:semesters,id',
            'academic_program' => ['required', Rule::in(array_values(Course::getAcademicPrograms()))],
            'version' => ['required', 'integer', Rule::in(array_keys(Course::getVersions()))],
            'name' => 'required|string|max:255',
            'credits' => 'required|integer',
            'type' => ['required', Rule::in(array_keys(Course::getTypes()))],
            'content' => 'nullable|string',
            'objectives' => 'nullable|json',
            'time_allocation' => 'nullable|json',
            'marks_allocation' => 'nullable|json',
            'ilos' => 'nullable|json',
            'urls' => 'nullable|json',
            'references' => 'nullable|json',
        ]);

        try {
            $course = Course::create($validatedData);
            return redirect()->route('dashboard.courses.index')->with('success', 'Course created successfully.');
        } catch (\Exception $e) {
            Log::error('Error creating course: ' . $e->getMessage());
            return abort(500);
        }
    }

    /**
     * Show the form for editing the specified course.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function edit(Course $course)
    {
        try {
            return view('backend.courses.edit', compact('course'));
        } catch (\Exception $e) {
            Log::error('Error loading course edit page: ' . $e->getMessage());
            return abort(500);
        }
    }
    /**
     * Update the specified course in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Course $course)
    {
        $validatedData = $request->validate([
            'code' => 'required|string|max:16|unique:courses,code,' . $course->id,
            'semester_id' => 'required|integer|exists:semesters,id',
            'academic_program' => ['required', Rule::in(array_values(Course::getAcademicPrograms()))],
            'version' => ['required', 'integer', Rule::in(array_keys(Course::getVersions()))],
            'name' => 'required|string|max:255',
            'credits' => 'required|integer',
            'type' => ['required', Rule::in(array_values(Course::getTypes()))],
            'type' => ['required', Rule::in(array_values(Course::getTypes()))],
            'content' => 'nullable|string',
            'objectives' => 'nullable|json',
            'time_allocation' => 'nullable|json',
            'marks_allocation' => 'nullable|json',
            'ilos' => 'nullable|json',
            'urls' => 'nullable|json',
            'references' => 'nullable|json',
        ]);
        try {
            $course->update($validatedData);
            return redirect()->route('dashboard.courses.index')->with('success', 'Course updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error updating course: ' . $e->getMessage());
            return abort(500);
        }
    }

    /**
     * Remove the specified course from storage.
     *
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Http\Response
     */
    public function delete(Course $course)
    {
        return view('backend.courses.delete', compact('course'));
    }

    public function destroy(Course $course)
    {
        try {
            $course->delete();
            return redirect()->route('dashboard.courses.index')->with('success', 'Course deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error in deleting course: ' . $e->getMessage());
            return abort(500);
        }
    }
}
