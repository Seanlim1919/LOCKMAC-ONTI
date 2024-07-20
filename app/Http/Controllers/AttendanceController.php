<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\StudentAttendance;

class AttendanceController extends Controller
{
    public function showFacultyAttendance()
    {
        $facultyAttendances = Attendance::with('faculty')->get();
        return view('admin.attendance', compact('facultyAttendances'));
    }

    public function showStudentAttendance()
    {
        $studentAttendances = StudentAttendance::with('student')->get();
        return view('faculty.attendance', compact('studentAttendances'));
    }
}
