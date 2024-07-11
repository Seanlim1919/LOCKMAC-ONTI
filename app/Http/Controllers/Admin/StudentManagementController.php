<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query();

        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_number', 'like', "%{$search}%")
                  ->orWhere('year_and_section', 'like', "%{$search}%")
                  ->orWhere('pc_number', 'like', "%{$search}%")
                  ->orWhere('gender', 'like', "%{$search}%");
            });
        }

        $students = $query->paginate(10);

        return view('admin.students.index', compact('students'));
    }

    public function create()
    {
        return view('admin.students.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_number' => 'required|string|unique:students',
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'program' => 'required|in:BSIT,BLIS,BSCS,BSIS',
            'year_and_section' => 'required|string',
            'gender' => 'required|in:male,female',
            'pc_number' => 'required|integer',
        ]);

        Student::create($data);

        return redirect()->route('admin.students.index')->with('success', 'Student added successfully');
    }

    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $data = $request->validate([
            'student_number' => 'required|string|unique:students,student_number,' . $student->id,
            'first_name' => 'required|string',
            'middle_name' => 'nullable|string',
            'last_name' => 'required|string',
            'program' => 'required|in:BSIT,BLIS,BSCS,BSIS',
            'year_and_section' => 'required|string',
            'gender' => 'required|in:male,female',
            'pc_number' => 'required|integer',
        ]);

        $student->update($data);

        return redirect()->route('admin.students.index')->with('success', 'Student updated successfully');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')->with('success', 'Student deleted successfully');
    }
}
