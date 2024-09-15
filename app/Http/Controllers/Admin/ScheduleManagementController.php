<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Course;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SchedulesExport;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

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
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);
    
        $start = Carbon::createFromFormat('H:i', $request->start_time);
        $end = Carbon::createFromFormat('H:i', $request->end_time);
        $duration = $end->diffInMinutes($start);    
    
        if ($duration > 180) {
            return back()->withErrors(['duration' => 'The schedule duration cannot exceed 3 hours.'])->withInput();
        }
    
        $conflictingSchedule = Schedule::where('day', $request->day)
            ->where(function($query) use ($request) {
                $query->where(function($query) use ($request) {
                    $query->where('start_time', '<', $request->end_time)
                          ->where('end_time', '>', $request->start_time);
                });
            })
            ->exists();
    
        if ($conflictingSchedule) {
            return back()->withErrors(['conflict' => 'This schedule is already occupied.'])->withInput();
        }
    
        \Log::info('Validated Data: ', $validated);
    
        $course = Course::find($request->course_id);
        $validated['course_code'] = $course->course_code;
        $validated['course_name'] = $course->course_name;
    
        Schedule::create($validated);
    
        return redirect()->route('admin.schedule.index')->with('success', 'Schedule created successfully.');
    }

    public function edit($id)
    {
        $schedule = Schedule::findOrFail($id); 
        $faculties = User::where('role', 'faculty')->get(); 
        $courses = Course::all();

        // Convert start_time and end_time to Carbon instances
        $schedule->start_time = $schedule->start_time ? Carbon::parse($schedule->start_time) : null;
        $schedule->end_time = $schedule->end_time ? Carbon::parse($schedule->end_time) : null;

        return view('admin.schedule.edit', compact('schedule', 'faculties', 'courses'));
    }
    
    

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'faculty_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'day' => 'required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'program' => 'required|in:BSIT,BLIS,BSCS,BSIS',
            'year' => 'required|in:1,2,3,4',
            'section' => 'required|in:A,B,C,D,E,F,G,H',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);
    
        $start = Carbon::createFromFormat('H:i', $request->start_time);
        $end = Carbon::createFromFormat('H:i', $request->end_time);
        $duration = $end->diffInMinutes($start);
    
        if ($duration > 180) {
            return back()->withErrors(['duration' => 'The schedule duration cannot exceed 3 hours.'])->withInput();
        }
    
        $conflictingSchedule = Schedule::where('day', $request->day)
            ->where(function($query) use ($request, $id) {
                $query->where(function($query) use ($request, $id) {
                    $query->where('start_time', '<', $request->end_time)
                          ->where('end_time', '>', $request->start_time)
                          ->where('id', '!=', $id);
                });
            })
            ->exists();
    
        if ($conflictingSchedule) {
            return back()->withErrors(['conflict' => 'This schedule is already occupied.'])->withInput();
        }
    
        $schedule = Schedule::findOrFail($id);
        $course = Course::find($request->course_id);
        $validated['course_code'] = $course->course_code;
        $validated['course_name'] = $course->course_name;
    
        $schedule->update($validated);
    
        return redirect()->route('admin.schedule.index')->with('success', 'Schedule updated successfully.');
    }
    
    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.schedule.index')->with('success', 'Schedule deleted successfully.');
    }

    public function exportPdf()
    {
        $schedules = Schedule::with('faculty')->get();

        $pdf = Pdf::loadView('admin.schedule.pdf', compact('schedules'))
                ->setPaper('a4', 'landscape'); 
        return $pdf->download('schedules.pdf');
    }
}
