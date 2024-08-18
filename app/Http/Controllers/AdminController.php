<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Course;
use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SchedulesExport;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        // Fetching all faculties with the 'faculty' role
        $faculties = User::where('role', 'faculty')->get();
        $facultyCount = $faculties->count();
        $studentCount = Student::count(); // Counting total students
        $courseCount = Course::count(); // Counting total courses
        $attendancePercentage = $this->getAttendancePercentage(); // Fetching overall attendance percentage

        $faculty_id = $request->input('faculty_id');
        $schedules = Schedule::when($faculty_id, function ($query, $faculty_id) {
            return $query->where('faculty_id', $faculty_id);
        })->with('course', 'faculty')->get();

        // Get monthly attendance data for faculty
        $monthlyAttendance = $this->getMonthlyAttendanceData();

        return view('admin.dashboard', compact(
            'schedules', 'faculties', 'facultyCount', 'studentCount', 'courseCount',
            'attendancePercentage', 'faculty_id', 'monthlyAttendance'
        ));
    }

    public function export()
    {
        return Excel::download(new SchedulesExport, 'schedules.xlsx');
    }

    private function getAttendancePercentage()
    {
        $currentMonth = Carbon::now()->month;
        $totalDays = Carbon::now()->daysInMonth;
        $totalFaculties = User::where('role', 'faculty')->count(); // Counting total faculties

        // Fetch all attendance records for the current month
        $attendances = Attendance::whereMonth('entered_at', $currentMonth)->get();

        // Grouping attendances by faculty_id and then by the day
        $facultyAttendances = $attendances->groupBy('faculty_id')->map(function ($attendance) {
            return $attendance->groupBy(function ($date) {
                return Carbon::parse($date->entered_at)->format('Y-m-d');
            })->count();
        });

        $totalAttendance = $facultyAttendances->sum(); // Summing up total attendance
        $possibleAttendance = $totalFaculties * $totalDays; // Calculating possible attendance

        return $possibleAttendance > 0 ? ($totalAttendance / $possibleAttendance) * 100 : 0;
    }

    private function getMonthlyAttendanceData()
    {
        $monthlyAttendance = Attendance::select(
            DB::raw("DATE_FORMAT(entered_at, '%Y-%m') as month"),
            DB::raw('COUNT(id) as attendance_count'),
            DB::raw('COUNT(DISTINCT faculty_id) as total_faculties') // Change user_id to faculty_id
        )
        ->whereNotNull('entered_at')
        ->whereNotNull('exited_at')
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $monthlyData = [];
        foreach ($monthlyAttendance as $data) {
            $totalDays = Carbon::parse($data->month . '-01')->daysInMonth;
            $possibleAttendance = $data->total_faculties * $totalDays;

            $monthlyData[] = [
                'month' => $data->month,
                'percentage' => $possibleAttendance > 0 ? ($data->attendance_count / $possibleAttendance) * 100 : 0
            ];
        }

        return $monthlyData;
    }
}
