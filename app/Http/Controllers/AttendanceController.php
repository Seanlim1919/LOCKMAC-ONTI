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

class AttendanceController extends Controller
{
    // Show Faculty Attendance
    public function showFacultyAttendance()
    {
        $facultyAttendances = Attendance::with('faculty')->get();
        return view('admin.attendance', compact('facultyAttendances'));
    }

    // Export Faculty Attendance
    public function exportFacultyAttendance()
    {
        return Excel::download(new FacultyAttendanceExport, 'faculty_attendance.xlsx');
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
