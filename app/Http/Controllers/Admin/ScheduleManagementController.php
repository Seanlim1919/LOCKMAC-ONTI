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
use App\Models\Semester;

class ScheduleManagementController extends Controller
{
    public function index(Request $request)
    {
        $defaultSemester = '1st'; 
        $defaultYearIn = date('Y');
        $defaultYearOut = $defaultYearIn + 1;
    
        $faculty_id = $request->input('faculty_id');
        $semester = $request->input('semester', $defaultSemester);      
        $year_in = $request->input('year_in', $defaultYearIn);    
        $year_out = $request->input('year_out', $defaultYearOut);  
        
        $faculties = User::where('role', 'faculty')->get();
    
        $schedules = Schedule::with('course', 'faculty', 'semester')
            ->when(is_null($faculty_id) && is_null($semester) && is_null($year_in) && is_null($year_out), function ($query) {
                return $query->where('status', 1);
            })
            ->when($faculty_id, function ($query, $faculty_id) {
                return $query->where('faculty_id', $faculty_id);
            })
            ->when($semester, function ($query, $semester) {
                return $query->whereHas('semester', function ($q) use ($semester) {
                    $q->where('semester_name', $semester . ' Semester');
                });
            })
            ->when($year_in, function ($query, $year_in) {
                return $query->whereHas('semester', function ($q) use ($year_in) {
                    $q->where('start_year', $year_in);
                });
            })
            ->when($year_out, function ($query, $year_out) {
                return $query->whereHas('semester', function ($q) use ($year_out) {
                    $q->where('end_year', $year_out);
                });
            })
            ->when(!is_null($faculty_id) || !is_null($semester) || !is_null($year_in) || !is_null($year_out), function ($query) {
                return $query->whereIn('status', [0, 1]);
            })
            ->paginate(10);
    
        return view('admin.schedule.index', compact('schedules', 'faculties', 'faculty_id', 'semester', 'year_in', 'year_out'));
    }
    
    
    
    
    
    
    public function useAll(Request $request)
    {
        $validatedData = $request->validate([
            'semester' => 'required|string|in:1st,2nd',
            'year_in' => 'required|integer|min:2020',
            'year_out' => 'required|integer|gte:year_in',
        ]);
    
        \Log::info('Validated Data:', $validatedData);
    
        $semesterQuery = Semester::where('semester_name', $request->input('semester') . ' Semester')
                                 ->where('start_year', $request->input('year_in'))
                                 ->where('end_year', $request->input('year_out'));
    
        \Log::info('Query being run:', ['sql' => $semesterQuery->toSql(), 'bindings' => $semesterQuery->getBindings()]);
    
        $semester = $semesterQuery->first();
    
        if (!$semester) {
            \Log::error('Semester not found', [
                'semester' => $request->input('semester') . ' Semester',
                'year_in' => $request->input('year_in'),
                'year_out' => $request->input('year_out'),
            ]);
        } else {
            \Log::info('Semester found:', $semester->toArray());
        }
    
        $resetSchedules = Schedule::query()->update(['status' => 0]);
    
        \Log::info('Reset Schedules', ['affected_rows' => $resetSchedules]);
    
        if ($semester) {
            $updatedSchedules = Schedule::where('semester_id', $semester->id)->update(['status' => 1]);
            \Log::info('Updated Schedules', ['affected_rows' => $updatedSchedules]);
        } else {
            \Log::warning('No specific schedules updated as no semester was found.');
        }
    
        return redirect()->route('admin.schedule.index')->with('success', 'Schedules status has been updated.');
    }
    
    
    

    #create

    public function create()
    {
        $faculties = User::where('role', 'faculty')->get();
        $courses = Course::all();
        return view('admin.schedule.create', compact('faculties', 'courses'));
    }


    #store

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
            'semester_name' => 'required|string', 
            'year_in' => 'required|integer|min:2020', 
            'year_out' => 'required|integer|gte:year_in', 
        ]);
    
        $start = Carbon::createFromFormat('H:i', $request->start_time);
        $end = Carbon::createFromFormat('H:i', $request->end_time);
        $duration = $end->diffInMinutes($start);
    
        if ($duration > 180) {
            return back()->withErrors(['duration' => 'The schedule duration cannot exceed 3 hours.'])->withInput();
        }
    
        $semester = Semester::firstOrCreate(
            [
                'semester_name' => $request->semester_name, 
                'start_year' => $request->year_in,
                'end_year' => $request->year_out
            ]
        );
    
        $conflictingSchedule = Schedule::where('day', $request->day)
            ->where('semester_id', $semester->id)
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
    
        Schedule::create([
            'faculty_id' => $validated['faculty_id'],
            'course_id' => $validated['course_id'],
            'course_code' => $validated['course_code'],
            'course_name' => $validated['course_name'],
            'day' => $validated['day'],
            'program' => $validated['program'],
            'year' => $validated['year'],
            'section' => $validated['section'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'semester_id' => $semester->id,  
            'status' => '0', 
        ]);
    
        return redirect()->route('admin.schedule.index')->with('success', 'Schedule created successfully.');
    }
    
    
    
    
    

    #edit

public function edit($id)
{
    $schedule = Schedule::findOrFail($id);
    $faculties = User::where('role', 'faculty')->get();
    $courses = Course::all();
    $semesters = Semester::all(); 

    $schedule->start_time = $schedule->start_time ? Carbon::parse($schedule->start_time) : null;
    $schedule->end_time = $schedule->end_time ? Carbon::parse($schedule->end_time) : null;
    return view('admin.schedule.edit', compact('schedule', 'faculties', 'courses', 'semesters'));
}

    
    #update

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
            'semester_name' => 'required|string',
            'year_in' => 'required|integer|min:2020',
            'year_out' => 'required|integer|gte:year_in',
        ]);
    
        $start = Carbon::createFromFormat('H:i', $request->start_time);
        $end = Carbon::createFromFormat('H:i', $request->end_time);
        $duration = $end->diffInMinutes($start);
    
        if ($duration > 180) {
            return back()->withErrors(['duration' => 'The schedule duration cannot exceed 3 hours.'])->withInput();
        }
    
        $semester = Semester::firstOrCreate(
            [
                'semester_name' => $request->semester_name,
                'start_year' => $request->year_in,
                'end_year' => $request->year_out
            ]
        );
    
        $conflictingSchedule = Schedule::where('day', $request->day)
            ->where('semester_id', $semester->id)
            ->where(function($query) use ($request, $id) {
                $query->where(function($query) use ($request) {
                    $query->where('start_time', '<', $request->end_time)
                          ->where('end_time', '>', $request->start_time);
                });
            })
            ->where('id', '!=', $id) 
            ->exists();
    
        if ($conflictingSchedule) {
            return back()->withErrors(['conflict' => 'This schedule is already occupied.'])->withInput();
        }
    
        \Log::info('Validated Data: ', $validated);
    
        $course = Course::find($request->course_id);
        $validated['course_code'] = $course->course_code;
        $validated['course_name'] = $course->course_name;
    
        $schedule = Schedule::findOrFail($id);
    
        $schedule->update([
            'faculty_id' => $validated['faculty_id'],
            'course_id' => $validated['course_id'],
            'course_code' => $validated['course_code'],
            'course_name' => $validated['course_name'],
            'day' => $validated['day'],
            'program' => $validated['program'],
            'year' => $validated['year'],
            'section' => $validated['section'],
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'semester_id' => $semester->id,  
            'status' => $schedule->status,   
        ]);
    
        return redirect()->route('admin.schedule.index')->with('success', 'Schedule updated successfully.');
    }
    
    
    #delete

    public function destroy(Schedule $schedule)
    {
        $schedule->delete();

        return redirect()->route('admin.schedule.index')->with('success', 'Schedule deleted successfully.');
    }

    


    #export
    public function exportPdf(Request $request)
    {
        $semester = $request->input('semester', '1st');
        $year_in = $request->input('year_in', date('Y'));
        $year_out = $request->input('year_out', $year_in + 1);
    
        $schedules = Schedule::whereHas('semester', function ($query) use ($semester, $year_in, $year_out) {
            $query->where('semester_name', $semester . ' Semester')
                  ->where('start_year', $year_in)
                  ->where('end_year', $year_out);
        })->with('faculty')->get();
    
        $pdf = Pdf::loadView('admin.schedule.pdf', compact('schedules', 'semester', 'year_in', 'year_out'))
                  ->setPaper('a4', 'landscape');
    
        return $pdf->stream('schedules.pdf');
    }

}
