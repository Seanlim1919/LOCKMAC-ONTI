<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Student;
use App\Models\Schedule;
use App\Models\StudentAttendance;
use App\Models\FacultyAttendance;
use Carbon\Carbon;

class RFIDController extends Controller
{
    public function scan(Request $request)
    {
        $rfid = $request->input('rfid');
        $now = Carbon::now();
        $day = $now->format('l');
        $time = $now->format('H:i:s');

        // Check if RFID belongs to a student
        $student = Student::where('rfid', $rfid)->first();
        if ($student) {
            $schedule = Schedule::where('program', $student->program)
                                ->where('year_and_section', $student->year_and_section)
                                ->where('day', $day)
                                ->where('start_time', '<=', $time)
                                ->where('end_time', '>=', $time)
                                ->first();
            if ($schedule) {
                StudentAttendance::create([
                    'student_id' => $student->id,
                    'entered_at' => $now
                ]);
                return response()->json(['status' => 'success', 'message' => 'Student attendance recorded.']);
            }
            return response()->json(['status' => 'failed', 'message' => 'No matching schedule.']);
        }

        // Check if RFID belongs to a faculty
        $faculty = User::where('rfid', $rfid)->where('role', 'faculty')->first();
        if ($faculty) {
            $schedule = Schedule::where('faculty_id', $faculty->id)
                                ->where('day', $day)
                                ->where('start_time', '<=', $time)
                                ->where('end_time', '>=', $time)
                                ->first();
            if ($schedule) {
                FacultyAttendance::create([
                    'faculty_id' => $faculty->id,
                    'entered_at' => $now
                ]);
                // Logic to unlock the door
                return response()->json(['status' => 'success', 'message' => 'Door unlocked.']);
            }
            return response()->json(['status' => 'failed', 'message' => 'No matching schedule.']);
        }

        return response()->json(['status' => 'failed', 'message' => 'RFID not recognized.']);
    }
}
