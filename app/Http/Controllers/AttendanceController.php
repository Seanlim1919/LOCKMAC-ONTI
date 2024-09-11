<?php

namespace App\Http\Controllers;

use App\Exports\FacultyAttendanceExport;
use App\Exports\StudentAttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Attendance;
use App\Models\StudentAttendance;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; // Add this at the top of your file


class AttendanceController extends Controller
{
    public function showFacultyAttendance(Request $request)
    {
        $date = now()->format('Y-m-d'); 

        $facultyAttendances = Attendance::whereHas('user', function($query) {
            $query->where('role', 'faculty');
        })
        ->whereDate('entered_at', $date) 
        ->with('user')
        ->orderBy('entered_at', 'asc') 
        ->paginate(10); 

        if ($facultyAttendances->isEmpty()) {
            $facultyAttendances = Attendance::whereHas('user', function($query) {
                $query->where('role', 'faculty');
            })
            ->with('user')
            ->orderBy('entered_at', 'asc') 
            ->paginate(10); 
        }

        return view('admin.attendance', compact('facultyAttendances'));
    }

    
    
    public function exportFacultyAttendance()
    {
        return Excel::download(new FacultyAttendanceExport, 'faculty_attendance.xlsx');
    }

    public function exportFacultyAttendancePdf()
    {
        $facultyAttendances = Attendance::with('user')->get();
    
        Log::info($facultyAttendances);
    
        if ($facultyAttendances->isEmpty()) {
            return response()->json(['message' => 'No attendance records found.'], 404);
        }
    
        $data = $facultyAttendances->map(function ($attendance) {
            $enteredAt = Carbon::parse($attendance->entered_at);
            $exitedAt = Carbon::parse($attendance->exited_at);
    
            return [
                'Faculty Name' => $attendance->user ? ($attendance->user->first_name . ' ' . $attendance->user->last_name) : 'N/A',
                'Date' => $enteredAt->format('Y-m-d'),
                'Time In' => $enteredAt->format('H:i:s'),
                'Time Out' => $exitedAt ? $exitedAt->format('H:i:s') : 'N/A',
            ];
        });
    
        $pdf = Pdf::loadView('admin.pdf', ['facultyAttendances' => $data]);
    
        return $pdf->download('faculty_attendance.pdf');
    }
    

    

    public function showStudentAttendance(Request $request)
    {
        $date = $request->input('date');
        $section = $request->input('section');
        $course = $request->input('course');
    
        $currentUser = auth()->user();
        
        $schedules = $currentUser->schedules;
    
        Log::info('Authenticated User:', [
            'user_id' => $currentUser->id,
            'user_name' => $currentUser->firstname . ' ' . $currentUser->lastname,
        ]);
    
        if ($schedules->isNotEmpty()) {
            foreach ($schedules as $schedule) {
                Log::info('Current Schedule:', [
                    'program' => $schedule->program,
                    'year' => $schedule->year,
                    'section' => $schedule->section
                ]);
            }
        } else {
            Log::info('Current Schedule:', ['message' => 'No schedule found for the user.']);
        }
    
        $query = StudentAttendance::with(['student', 'course']);
            if ($schedules->isNotEmpty()) {
            $query->whereHas('student', function ($q) use ($schedules) {
                $q->where(function($query) use ($schedules) {
                    foreach ($schedules as $schedule) {
                        $query->orWhere(function($q) use ($schedule) {
                            $q->where('program', $schedule->program)
                              ->where('year', $schedule->year)
                              ->where('section', $schedule->section);
                        });
                    }
                });
            });
        }
            if ($date) {
            $query->whereDate('entered_at', $date);
        }
        if ($section) {
            $query->whereHas('student', function ($q) use ($section) {
                $q->where('section', $section);
            });
        }
    
        if ($course) {
            $query->whereHas('course', function ($q) use ($course) {
                $q->where('id', $course);
            });
        }
            $studentAttendances = $query->get();
            $sections = Student::distinct()->pluck('section');
        $courses = Course::all(); 
    
        return view('faculty.attendance', compact('studentAttendances', 'date', 'section', 'course', 'sections', 'courses'));
    }
    

    public function index(Request $request)
    {
        $date = $request->input('date');
        $course = $request->input('course');
        $program = $request->input('program');
        $year = $request->input('year');
        $section = $request->input('section');
        $search = $request->input('search');
        

        $query = StudentAttendance::with(['student', 'course'])
            ->when($date, function ($q) use ($date) {
                $q->whereDate('entered_at', $date);
            })
            ->when($course, function ($q) use ($course) {
                $q->where('course_id', $course);
            })
            ->when($program, function ($q) use ($program) {
                $q->whereHas('student', function ($q) use ($program) {
                    $q->where('program', $program);
                });
            })
            ->when($year, function ($q) use ($year) {
                $q->whereHas('student', function ($q) use ($year) {
                    $q->where('year', $year);
                });
            })
            ->when($section, function ($q) use ($section) {
                $q->whereHas('student', function ($q) use ($section) {
                    $q->where('section', $section);
                });
            })
            ->when($search, function ($q) use ($search) {
                $q->whereHas('student', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('student_number', 'like', "%{$search}%");
                });
            });

        $studentAttendances = $query->get();

        $courses = Course::all();
        $sections = Student::distinct()->pluck('section');
        

        return view('faculty.attendance', compact('studentAttendances', 'courses', 'sections', 'search'));
    }

    public function export(Request $request)
    {
        $date = $request->input('date');
        $course = $request->input('course');
        $program = $request->input('program');
        $year = $request->input('year');
        $section = $request->input('section');

        return Excel::download(new StudentAttendanceExport($date, $course, $program, $year, $section), 'student_attendance.xlsx');
    }


    public function exportLogbookPdf(Request $request)
    {
        $date = $request->input('date');
        $section = $request->input('section');
        $course = $request->input('course');
        $program = $request->input('program');
        $year = $request->input('year');
    
        $query = StudentAttendance::with(['student', 'course', 'faculty']); 
    
        if ($date) {
            $query->whereDate('entered_at', $date);
        }
    
        if ($section) {
            $query->whereHas('student', function ($q) use ($section) {
                $q->where('section', $section);
            });
        }
    
        if ($course) {
            $query->whereHas('course', function ($q) use ($course) {
                $q->where('id', $course);
            });
        }
    
        if ($program) {
            $query->whereHas('student', function ($q) use ($program) {
                $q->where('program', $program);
            });
        }
    
        if ($year) {
            $query->whereHas('student', function ($q) use ($year) {
                $q->where('year', $year);
            });
        }
    
        $studentAttendances = $query->get();
    
        $pdf = Pdf::loadView('faculty.student_logbook', compact('studentAttendances'))
                    ->setPaper('a4', 'landscape');
    
        return $pdf->download('student_logbook.pdf');
    }

}
