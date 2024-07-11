<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Excel;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $gender = $request->input('gender');
        $program = $request->input('program');
        $year_and_section = $request->input('year_and_section');

        $students = Student::query()
            ->when($search, function ($query, $search) {
                return $query->where(function($q) use ($search) {
                    $q->where('first_name', 'LIKE', "%{$search}%")
                      ->orWhere('last_name', 'LIKE', "%{$search}%")
                      ->orWhere('student_number', 'LIKE', "%{$search}%")
                      ->orWhere('program', 'LIKE', "%{$search}%")
                      ->orWhere('year_and_section', 'LIKE', "%{$year_and_section}%");
                });
            })
            ->when($gender, function ($query, $gender) {
                return $query->where('gender', $gender);
            })
            ->when($program, function ($query, $program) {
                return $query->where('program', $program);
            })
            ->when($year_and_section, function ($query, $year_and_section) {
                return $query->where('year_and_section', 'LIKE', "%{$year_and_section}%");
            })
            ->paginate(10);

        return view('faculty.students.index', compact('students', 'search', 'gender', 'program', 'year_and_section'));
    }

    public function create()
    {
        return view('faculty.students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_number' => 'required|unique:students',
            'first_name' => 'required',
            'last_name' => 'required',
            'program' => 'required|in:BSIT,BLIS,BSCS,BSIS',
            'year_and_section' => 'required',
            'gender' => 'required|in:male,female',
            'pc_number' => 'required|integer',
        ]);

        Student::create($request->all());
        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    public function update(Request $request, Student $student)
    {
        $request->validate([
            'student_number' => 'required|unique:students,student_number,' . $student->id,
            'first_name' => 'required',
            'last_name' => 'required',
            'program' => 'required|in:BSIT,BLIS,BSCS,BSIS',
            'year_and_section' => 'required',
            'gender' => 'required|in:male,female',
            'pc_number' => 'required|integer',
        ]);

        $student->update($request->all());
        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    public function edit(Student $student)
    {
        return view('faculty.students.edit', compact('student'));
    }

    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Student deleted successfully.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,csv,ods,xls'
        ]);

        $path = $request->file('file')->getRealPath();

        Excel::load($path, function($reader) {
            $results = $reader->get();
            foreach ($results as $row) {
                Student::create([
                    'student_number' => $row['student_number'],
                    'first_name'     => $row['first_name'],
                    'last_name'      => $row['last_name'],
                    'program'        => $row['program'],
                    'year_and_section' => $row['year_and_section'],
                    'pc_number'      => $row['pc_number'],
                ]);
            }
        });

        return redirect()->route('students.index')->with('success', 'Students imported successfully.');
    }

    public function importPDF(Request $request)
    {
        // Implement your PDF import logic here
    }
}
