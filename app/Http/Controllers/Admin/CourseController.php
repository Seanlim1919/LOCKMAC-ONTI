<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $courses = Course::query()
            ->when($search, function ($query, $search) {
                return $query->where('course_name', 'like', "%{$search}%")
                             ->orWhere('course_code', 'like', "%{$search}%")
                             ->orWhere('program', 'like', "%{$search}%");
            })
            ->paginate(10);

        return view('admin.course.index', compact('courses', 'search'));
    }

    public function create()
    {
        $programs = ['BSIT', 'BLIS', 'BSCS', 'BSIS'];
        return view('admin.course.create', compact('programs'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_name' => 'required|string|max:255',
            'course_code' => 'required|string|max:255|unique:courses',
            'program' => 'required|in:BSIT,BLIS,BSCS,BSIS',
        ]);

        Course::create($request->all());

        return redirect()->route('admin.course.index')->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        $programs = ['BSIT', 'BLIS', 'BSCS', 'BSIS'];
        return view('admin.course.edit', compact('course', 'programs'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'course_name' => 'required|string|max:255',
            'course_code' => 'required|string|max:255|unique:courses,course_code,' . $course->id,
            'program' => 'required|in:BSIT,BLIS,BSCS,BSIS',
        ]);

        $course->update($request->all());

        return redirect()->route('admin.course.index')->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $course->delete();

        return redirect()->route('admin.course.index')->with('success', 'Course deleted successfully.');
    }
}
