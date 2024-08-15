<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;

class AccessController extends Controller
{
    public function checkAccess(Request $request)
    {
        $rfidCode = $request->input('rfid'); // Assuming the RFID code is provided
        $user = User::whereHas('rfid', function($query) use ($rfidCode) {
            $query->where('rfid_code', $rfidCode);
        })->first();

        if ($user && ($user->role == 'admin' || $user->role == 'faculty')) {
            return response()->json(['status' => 'allowed', 'user_id' => $user->id]);
        } else {
            return response()->json(['status' => 'denied']);
        }
    }

    public function logAttendance(Request $request)
    {
        $user_id = $request->input('user_id');
        $attendance = new Attendance();
        $attendance->user_id = $user_id;
        $attendance->entered_at = now();
        $attendance->save();

        return response()->json(['status' => 'logged']);
    }
}
