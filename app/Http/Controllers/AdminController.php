<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\User;
use App\Models\Course;
use App\Models\Student;
use App\Models\StudentAttendance; // Ensure this is correct
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\SchedulesExport;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index(Request $request)
    {
        $faculties = User::where('role', 'faculty')->get();
        $facultyCount = $faculties->count();
        $studentCount = Student::count(); 
        $courseCount = Course::count(); 
        $attendancePercentage = $this->getAttendancePercentage(); 
        $studentAttendanceToday = $this->getStudentAttendancePercentageToday();

        $faculty_id = $request->input('faculty_id');
        $schedules = Schedule::when($faculty_id, function ($query, $faculty_id) {
            return $query->where('faculty_id', $faculty_id);
        })->with('course', 'faculty')->get();

        $monthlyAttendance = $this->getMonthlyAttendanceData();
        
        // Assuming you want to pass similar data for faculty attendance, student attendance, and system visitors
        $facultyAttendanceData = $this->getAttendanceData('faculty');
        $studentAttendanceData = $this->getAttendanceData('student');

        return view('admin.dashboard', compact(
            'schedules', 'faculties', 'facultyCount', 'studentCount', 'courseCount',
            'attendancePercentage', 'faculty_id', 'monthlyAttendance', 'studentAttendanceToday',
            'facultyAttendanceData', 'studentAttendanceData'
        ));
    }

    private function getAttendanceData($type)
    {
        // Sample implementation for fetching data without 'type' column
        // Assuming you want to fetch attendance data grouped by date
        $data = StudentAttendance::select(
            DB::raw("DATE_FORMAT(entered_at, '%Y-%m-%d') as date"),
            DB::raw('COUNT(id) as count')
        )
        ->groupBy('date')
        ->orderBy('date')
        ->get();

        return [
            'labels' => $data->pluck('date')->toArray(),
            'data' => $data->pluck('count')->toArray()
        ];
    }


    

    public function export()
    {
        return Excel::download(new SchedulesExport, 'schedules.xlsx');
    }

    private function getAttendancePercentage()
    {
        $currentMonth = Carbon::now()->month;
        $totalDays = Carbon::now()->daysInMonth;
        $totalFaculties = User::where('role', 'faculty')->count();

        $attendances = StudentAttendance::whereMonth('entered_at', $currentMonth)->get();

        $facultyAttendances = $attendances->groupBy('faculty_id')->map(function ($attendance) {
            return $attendance->groupBy(function ($date) {
                return Carbon::parse($date->entered_at)->format('Y-m-d');
            })->count();
        });

        $totalAttendance = $facultyAttendances->sum(); 
        $possibleAttendance = $totalFaculties * $totalDays; 

        return $possibleAttendance > 0 ? ($totalAttendance / $possibleAttendance) * 100 : 0;
    }

    private function getStudentAttendancePercentageToday()
    {
        $today = Carbon::today();
        $totalStudents = Student::count();
        $attendancesToday = StudentAttendance::whereDate('entered_at', $today->toDateString())
            ->distinct('student_id')
            ->count('student_id');

        return $totalStudents > 0 ? ($attendancesToday / $totalStudents) * 100 : 0;
    }

    private function getMonthlyAttendanceData()
    {
        $monthlyAttendance = StudentAttendance::select(
            DB::raw("DATE_FORMAT(entered_at, '%Y-%m') as month"),
            DB::raw('COUNT(id) as attendance_count'),
            DB::raw('COUNT(DISTINCT student_id) as total_students')
        )
        ->whereNotNull('entered_at')
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        $monthlyData = [];
        foreach ($monthlyAttendance as $data) {
            $totalDays = Carbon::parse($data->month . '-01')->daysInMonth;
            $possibleAttendance = $data->total_students * $totalDays;

            $monthlyData[] = [
                'month' => $data->month,
                'percentage' => $possibleAttendance > 0 ? ($data->attendance_count / $possibleAttendance) * 100 : 0
            ];
        }

        return $monthlyData;
    }

}
