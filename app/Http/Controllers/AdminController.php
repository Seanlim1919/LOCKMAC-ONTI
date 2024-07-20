<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Course;
use App\Models\Attendance;
use App\Models\Student; // Import the Student model
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SchedulesExport;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $faculties = User::where('role', 'faculty')->get();
        $facultyCount = $faculties->count();
        $studentCount = Student::count(); // Count from the students table
        $courseCount = Course::count();
        $attendancePercentage = $this->getAttendancePercentage();

        $faculty_id = $request->input('faculty_id');
        $schedules = Schedule::when($faculty_id, function ($query, $faculty_id) {
            return $query->where('faculty_id', $faculty_id);
        })->with('course', 'faculty')->get();

        return view('admin.dashboard', compact('schedules', 'faculties', 'facultyCount', 'studentCount', 'courseCount', 'attendancePercentage', 'faculty_id'));
    }

    public function export()
    {
        return Excel::download(new SchedulesExport, 'schedules.xlsx');
    }

    private function getAttendancePercentage()
    {
        $currentMonth = Carbon::now()->month;
        $totalDays = Carbon::now()->daysInMonth;
        $totalStudents = Student::count(); // Count from the students table

        // Get all attendance records for the current month
        $attendances = Attendance::whereMonth('entered_at', $currentMonth)->get();

        // Calculate the number of unique days each student has attended
        $studentAttendances = $attendances->groupBy('user_id')->map(function ($attendance) {
            return $attendance->groupBy(function ($date) {
                return Carbon::parse($date->entered_at)->format('Y-m-d');
            })->count();
        });

        // Calculate total attendance for all students
        $totalAttendance = $studentAttendances->sum();

        // Calculate the possible total attendance
        $possibleAttendance = $totalStudents * $totalDays;

        return $possibleAttendance > 0 ? ($totalAttendance / $possibleAttendance) * 100 : 0;
    }
}
