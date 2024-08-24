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
    // Show Faculty Attendance
    public function showFacultyAttendance(Request $request)
    {
        $date = now()->format('Y-m-d'); // Get the current date in 'YYYY-MM-DD' format

        // Attempt to fetch today's attendance
        $facultyAttendances = Attendance::whereHas('user', function($query) {
            $query->where('role', 'faculty');
        })
        ->whereDate('entered_at', $date) // Filter by today's date
        ->with('user')
        ->orderBy('entered_at', 'asc') // Order by time
        ->paginate(10); // Paginate results with 10 per page

        // Check if no records are found for today's date
        if ($facultyAttendances->isEmpty()) {
            // If no records for today, fetch all attendance records
            $facultyAttendances = Attendance::whereHas('user', function($query) {
                $query->where('role', 'faculty');
            })
            ->with('user')
            ->orderBy('entered_at', 'asc') // Order by time
            ->paginate(10); // Paginate results with 10 per page
        }

        return view('admin.attendance', compact('facultyAttendances'));
    }

    
    
    // Export Faculty Attendance
    public function exportFacultyAttendance()
    {
        return Excel::download(new FacultyAttendanceExport, 'faculty_attendance.xlsx');
    }

    public function exportFacultyAttendancePdf()
    {
        // Fetch all attendance data with related faculty
        $facultyAttendances = Attendance::with('user')->get();
    
        // Log the data to check if it's being retrieved correctly
        Log::info($facultyAttendances);
    
        // Ensure the data is not empty
        if ($facultyAttendances->isEmpty()) {
            return response()->json(['message' => 'No attendance records found.'], 404);
        }
    
        // Transform the data
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
    
        // Load the view and pass the data
        $pdf = Pdf::loadView('admin.pdf', ['facultyAttendances' => $data]);
    
        // Return the PDF as a download
        return $pdf->download('faculty_attendance.pdf');
    }
    

    

    // Show Student Attendance with filters
    public function showStudentAttendance(Request $request)
    {
        // Fetch filters
        $date = $request->input('date');
        $section = $request->input('section');
        $course = $request->input('course');

        // Query student attendance based on the filters
        $query = StudentAttendance::with(['student', 'course']);

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

        // Get sections and courses for filters
        $sections = Student::distinct()->pluck('section');
        $courses = Course::all(); // Fetch all courses

        return view('faculty.attendance', compact('studentAttendances', 'date', 'section', 'course', 'sections', 'courses'));
    }

    // Index for Student Attendance with filters
    public function index(Request $request)
    {
        // Get filters
        $date = $request->input('date');
        $course = $request->input('course');
        $program = $request->input('program');
        $year = $request->input('year');
        $section = $request->input('section');
        $search = $request->input('search');
        

        // Query to get student attendances based on filters
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

        // Get sections and courses for filters
        $courses = Course::all();
        $sections = Student::distinct()->pluck('section');

        return view('faculty.attendance', compact('studentAttendances', 'courses', 'sections', 'search'));
    }

    // Export Student Attendance based on filters
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
        // Fetch filters
        $date = $request->input('date');
        $section = $request->input('section');
        $course = $request->input('course');
        $program = $request->input('program');
        $year = $request->input('year');

        // Query student attendance based on the filters
        $query = StudentAttendance::with(['student', 'course', 'student.faculty']);

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

        // Load the view and pass the data to the PDF
        $pdf = Pdf::loadView('faculty.student_logbook', compact('studentAttendances'))
                    ->setPaper('a4', 'landscape');

        // Download the PDF file
        return $pdf->download('student_logbook.pdf');
    }

}
