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
            return redirect()->route('dashboard.courses.index')->with('Success', 'Course deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error in deleting course: ' . $e->getMessage());
            return abort(500);
        }
    }
}