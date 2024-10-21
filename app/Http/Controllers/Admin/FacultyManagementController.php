<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Models\RFID;
use App\Models\Student;
use Illuminate\Database\Eloquent\SoftDeletes;

class FacultyManagementController extends Controller
{
    use SoftDeletes;

    public function index(Request $request)
    {
        $search = $request->input('search');
        $query = User::where('role', 'faculty')
        ->where('status', '!=', 'Deleted')
        ->withTrashed(); 



        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        $faculties = $query->paginate(10);

        return view('admin.faculty.index', compact('faculties', 'search'));
    }

    public function create()
    {
        return view('admin.faculty.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'required|string|max:15',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
        ]);

        User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
            'role' => 'faculty',
        ]);

        return redirect()->route('admin.faculty.index')->with('success', 'Faculty created successfully.');
    }

    public function edit(Request $request, $id)
    {
        $faculty = User::with('rfid')->findOrFail($id);
        $rfid = Rfid::find($faculty->rfid_id);
        $clientIp = $request->getClientIp();

        return view('admin.faculty.edit', compact('faculty','rfid'),['clientIp' => $clientIp]);
    }
    
    

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone_number' => 'required|string|max:15',
            'gender' => 'required|in:male,female',
            'date_of_birth' => 'required|date',
        ]);

        $faculty = User::findOrFail($id);
        $faculty->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'date_of_birth' => $request->date_of_birth,
        ]);

        return redirect()->route('admin.faculty.index')->with('success', 'Faculty updated successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $faculty = User::findOrFail($id);
        $faculty->status = $request->input('status');
        $faculty->save();

        return redirect()->back()->with('success', 'Faculty status updated successfully.');
    }


    public function destroy($id)
    {
        $faculty = User::findOrFail($id);
        
        // Check if the faculty has any active schedules
        if ($faculty->schedules()->where('status', 'active')->exists()) {
            return redirect()->route('admin.faculty.index')->with('error', "{$faculty->first_name} {$faculty->last_name} has an existing schedule; deletion of account is not allowed.");
        }
        
        // Disable the faculty instead of deleting
        $faculty->status = 'Deleted';
        $faculty->save();
        
        return redirect()->route('admin.faculty.index')->with('success', 'Faculty disabled successfully.');
    }
    
    
    
    
    
}
