<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;
use App\Domains\Course\Models\Course;
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
        Log::debug('Entering CourseController@index');
        $courses = Course::all();
        Log::debug('Fetched all courses', ['count' => $courses->count()]);
        return view('backend.courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new course.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        Log::debug('Entering CourseController@create');
        return view('backend.courses.create');
    }

    /**
     * Store a newly created course in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        Log::debug('Entering CourseController@store', ['request' => $request->all()]);
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
        Log::debug('Validated data', ['data' => $validatedData]);
        try {
            $course = Course::create($validatedData);
            Log::info('Course created successfully', ['course_id' => $course->id]);
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
        Log::debug('Entering CourseController@edit', ['course_id' => $course->id]);
        return view('backend.courses.edit', compact('course'));
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
        Log::debug('Entering CourseController@update', ['course_id' => $course->id, 'request' => $request->all()]);
        $validatedData = $request->validate([
            'code' => 'required|string|max:16|unique:courses,code,' . $course->id,
            'semester_id' => 'required|integer|exists:semesters,id',
            'academic_program' => ['required', Rule::in(array_values(Course::getAcademicPrograms()))],
            'version' => ['required', 'integer', Rule::in(array_keys(Course::getVersions()))],
            'name' => 'required|string|max:255',
            'credits' => 'required|integer',
            'type' => ['required',Rule::in(array_values(Course::getTypes()))],
            'type' => ['required',Rule::in(array_values(Course::getTypes()))],
            'content' => 'nullable|string',
            'objectives' => 'nullable|json',
            'time_allocation' => 'nullable|json',
            'marks_allocation' => 'nullable|json',
            'ilos' => 'nullable|json',
            'urls' => 'nullable|json',
            'references' => 'nullable|json',
        ]);
        Log::debug('Validated data', ['data' => $validatedData]);
        try {
            $course->update($validatedData);
            Log::info('Course updated successfully', ['course_id' => $course->id]);
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
        Log::debug('Entering CourseController@delete', ['course_id' => $course->id]);
        return view('backend.courses.delete', compact('course'));
    }

    public function destroy(Course $course)
    {
        Log::debug('Entering CourseController@destroy', ['course_id' => $course->id]);
        try {
            $course->delete();
            Log::info('Course deleted successfully', ['course_id' => $course->id]);
            return redirect()->route('dashboard.courses.index')->with('success', 'Course deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error in deleting course: ' . $e->getMessage());
            return abort(500);
        }
    }
}