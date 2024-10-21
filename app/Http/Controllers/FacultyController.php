<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Schedule;
use Illuminate\Support\Facades\Auth;

class FacultyController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        $schedules = Schedule::where('faculty_id', $user->id)
                             ->where('status', 1) 
                             ->with('course', 'faculty')
                             ->get();
    
        return view('faculty.dashboard', compact('schedules'));
    }
    
}
