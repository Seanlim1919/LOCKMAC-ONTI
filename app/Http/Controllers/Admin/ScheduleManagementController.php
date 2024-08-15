<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SchedulesExport;

class ScheduleManagementController extends Controller
{
    public function index(Request $request)
    {
        $faculties = User::where('role', 'faculty')->get();
        $faculty_id = $request->input('faculty_id');
        $schedules = Schedule::when($faculty_id, function ($query, $faculty_id) {
            return $query->where('faculty_id', $faculty_id);
        })->with('course', 'faculty')->paginate(10);

        return view('admin.schedule.index', compact('schedules', 'faculties', 'faculty_id'));
    }

    public function create()
    {
        $faculties = User::where('role', 'faculty')->get();
        $courses = Course::all();
        return view('admin.schedule.create', compact('faculties', 'courses'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'program' => 'required|in:BSIT,BLIS,BSCS,BSIS',
            'year' => 'required|in:1,2,3,4',
            'section' => 'required|in:A,B,C,D,E,F,G,H',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i',
        ]);

        $course = Course::find($request->course_id);
        $validated['course_code'] = $course->course_code;
        $validated['course_name'] = $course->course_name;

        Schedule::create($validated);

        return redirect()->route('admin.schedule.index')->with('success', 'Schedule created successfully.');
    }

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.schedule.index')->with('success', 'Schedule deleted successfully.');
    }

    public function export()
    {
        return Excel::download(new SchedulesExport, 'schedules.xlsx');
    }
}
