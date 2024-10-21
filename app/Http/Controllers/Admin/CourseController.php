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
        $sem_avail = $request->input('sem_avail');
        $year_avail = $request->input('year_avail');

        $courses = Course::query()
            ->when($search, function ($query, $search) {
                return $query->where('course_name', 'like', "%{$search}%")
                             ->orWhere('course_code', 'like', "%{$search}%")
                             ->orWhere('program', 'like', "%{$search}%");
            })
            ->when($sem_avail, function ($query, $sem_avail) {
                return $query->where('sem_avail', $sem_avail);
            })
            ->when($year_avail, function ($query, $year_avail) {
                return $query->where('year_avail', $year_avail);
            })
            ->paginate(10);

        return view('admin.course.index', compact('courses', 'search', 'sem_avail', 'year_avail'));
    }

    public function create()
    {
        $programs = ['BSIT', 'BLIS', 'BSCS', 'BSIS'];
        $semesters = ['First', 'Second'];
        $years = [1, 2, 3, 4]; 
        return view('admin.course.create', compact('programs', 'semesters', 'years'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_name' => 'required|string|max:255',
            'course_code' => 'required|string|max:255|unique:courses',
            'program' => 'required|in:BSIT,BLIS,BSCS,BSIS',
            'sem_avail' => 'required|in:First,Second',
            'year_avail' => 'required|in:1,2,3,4', 
        ]);

        Course::create($request->all());

        return redirect()->route('admin.course.index')->with('success', 'Course created successfully.');
    }

    public function edit(Course $course)
    {
        $programs = ['BSIT', 'BLIS', 'BSCS', 'BSIS'];
        $semesters = ['First', 'Second'];
        $years = [1, 2, 3, 4]; // Year levels
        return view('admin.course.edit', compact('course', 'programs', 'semesters', 'years'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'course_name' => 'required|string|max:255',
            'course_code' => 'required|string|max:255|unique:courses,course_code,' . $course->id,
            'program' => 'required|in:BSIT,BLIS,BSCS,BSIS',
            'sem_avail' => 'required|in:First,Second',
            'year_avail' => 'required|in:1,2,3,4', // Validation for year level
        ]);

        $course->update($request->all());

        return redirect()->route('admin.course.index')->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        $course->delete(); // Use the Eloquent `delete()` method
 
        // Optionally redirect or send a response
        return redirect()->route('admin.course.index')->with('success', 'Course deleted successfully.');
    }
}
