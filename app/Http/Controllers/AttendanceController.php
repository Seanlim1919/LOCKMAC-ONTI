<?php

namespace App\Http\Controllers;

use App\Exports\FacultyAttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Attendance;

class AttendanceController extends Controller
{
    public function showFacultyAttendance()
    {
        $facultyAttendances = Attendance::with('faculty')->get();
        return view('admin.attendance', compact('facultyAttendances'));
    }

    public function exportFacultyAttendance()
    {
        return Excel::download(new FacultyAttendanceExport, 'faculty_attendance.xlsx');
    }

    public function showStudentAttendance()
    {
        $studentAttendances = StudentAttendance::with('student')->get();
        return view('faculty.attendance', compact('studentAttendances'));
    }
}
