<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class FacultyManagementController extends Controller
{
    public function index()
    {
        $faculties = User::where('role', 'faculty')->paginate(10);
        return view('admin.faculty.index', compact('faculties'));
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
        ]);

        User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'role' => 'faculty',
        ]);

        return redirect()->route('admin.faculty.index')->with('success', 'Faculty created successfully.');
    }

    public function edit($id)
    {
        $faculty = User::findOrFail($id);
        return view('admin.faculty.edit', compact('faculty'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $id,
            'phone_number' => 'required|string|max:15',
            'gender' => 'required|in:male,female',
        ]);

        $faculty = User::findOrFail($id);
        $faculty->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
        ]);

        return redirect()->route('admin.faculty.index')->with('success', 'Faculty updated successfully.');
    }

    public function destroy($id)
    {
        $faculty = User::findOrFail($id);
        $faculty->delete();

        return redirect()->route('admin.faculty.index')->with('success', 'Faculty deleted successfully.');
    }
}
