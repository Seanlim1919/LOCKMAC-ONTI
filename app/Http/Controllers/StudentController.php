<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $gender = $request->input('gender');
        $program = $request->input('program');
        $year = $request->input('year');
        $section = $request->input('section');
        $scheduleId = $request->input('schedule_id');

        $currentUser = auth()->user();
        $schedules = $currentUser->schedules; // Retrieve all schedules for the current user

        Log::info('Authenticated User:', [
            'user_id' => $currentUser->id,
            'user_name' => $currentUser->first_name . ' ' . $currentUser->last_name,
        ]);

        if ($schedules->isNotEmpty()) {
            foreach ($schedules as $schedule) {
                Log::info('Current Schedule:', [
                    'program' => $schedule->program,
                    'year' => $schedule->year,
                    'section' => $schedule->section
                ]);
            }
        } else {
            Log::info('Current Schedule:', ['message' => 'No schedule found for the user.']);
        }

        // Apply current schedule filters if no specific filters are provided
        if (!$program && !$year && !$section && $schedules->isNotEmpty()) {
            $programs = $schedules->pluck('program')->unique();
            $years = $schedules->pluck('year')->unique();
            $sections = $schedules->pluck('section')->unique();

            $students = Student::query()
                ->whereIn('program', $programs)
                ->whereIn('year', $years)
                ->whereIn('section', $sections);
        } else {
            $students = Student::query()
                ->when($search, function ($query, $search) {
                    return $query->where(function($q) use ($search) {
                        $q->where('first_name', 'LIKE', "%{$search}%")
                            ->orWhere('last_name', 'LIKE', "%{$search}%")
                            ->orWhere('student_number', 'LIKE', "%{$search}%")
                            ->orWhere('program', 'LIKE', "%{$search}%")
                            ->orWhere('year', 'LIKE', "%{$search}%")
                            ->orWhere('section', 'LIKE', "%{$search}%");
                    });
                })
                ->when($gender, function ($query, $gender) {
                    return $query->where('gender', $gender);
                })
                ->when($program, function ($query, $program) {
                    return $query->where('program', $program);
                })
                ->when($year, function ($query, $year) {
                    return $query->where('year', $year);
                })
                ->when($section, function ($query, $section) {
                    return $query->where('section', $section);
                })
                ->when($scheduleId, function ($query, $scheduleId) {
                    return $query->where('schedule_id', $scheduleId);
                })
                ->orderBy('program')
                ->orderBy('year')
                ->orderBy('section');
        }

        // Paginate students
        $students = $students->paginate(10);

        $allSchedules = Schedule::all(); // Load all schedules for other purposes

        return view('faculty.students.index', compact('students', 'search', 'gender', 'program', 'year', 'section', 'scheduleId', 'allSchedules'));
    }




    public function create()
    {
        return view('faculty.students.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'student_number' => 'required|unique:students',
            'first_name' => 'required|regex:/^[a-zA-Z]{2,}$/',
            'middle_name' => 'nullable|regex:/^[a-zA-Z]{2,}$/',
            'last_name' => 'required|regex:/^[a-zA-Z]{2,}$/',
            'program' => 'required|in:BSIT,BLIS,BSCS,BSIS',
            'year' => 'required|in:1,2,3,4',
            'section' => 'required|in:A,B,C,D,E,F,G,H',
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
            'first_name' => 'required|regex:/^[a-zA-Z]{2,}$/',
            'middle_name' => 'nullable|regex:/^[a-zA-Z]{2,}$/',
            'last_name' => 'required|regex:/^[a-zA-Z]{2,}$/',
            'program' => 'required|in:BSIT,BLIS,BSCS,BSIS',
            'year' => 'required|in:1,2,3,4',
            'section' => 'required|in:A,B,C,D,E,F,G,H',
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
        $spreadsheet = IOFactory::load($path);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();
        $errors = [];

        foreach ($rows as $index => $row) {
            if ($index == 0) {
                // Skip header row
                continue;
            }

            // Skip rows with invalid names
            if (!preg_match('/^[a-zA-Z]{2,}$/', $row[1]) || 
                !preg_match('/^[a-zA-Z]{2,}$/', $row[2]) || 
                !preg_match('/^[a-zA-Z]{2,}$/', $row[3])) {
                $errors[] = "Invalid name format in row $index";
                continue;
            }

            $studentData = [
                'student_number' => $row[0],
                'first_name' => $row[1],
                'middle_name' => $row[2],
                'last_name' => $row[3],
                'program' => $row[4],
                'year' => substr($row[5], 0, 1),
                'section' => substr($row[5], 1),
                'pc_number' => $row[6],
            ];

            try {
                Student::create($studentData);
            } catch (\Exception $e) {
                $errors[] = "Failed to create student with student number {$row[0]}: {$e->getMessage()}";
                Log::error("Failed to create student: " . json_encode($studentData) . " Error: " . $e->getMessage());
            }
        }

        if (!empty($errors)) {
            return redirect()->route('students.index')->with('errors', $errors);
        }

        return redirect()->route('students.index')->with('success', 'Students imported successfully.');
    }

    public function importPDF(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:pdf'
        ]);

        $path = $request->file('file')->getRealPath();
        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        $text = $pdf->getText();
        $errors = [];

        // Remove any unwanted characters and clean up the text
        $lines = explode("\n", $text);
        Log::info("PDF Lines: " . json_encode($lines));
        foreach ($lines as $line) {
            // Regular expression to match the required format
            if (preg_match('/(\d{6})([A-Z][a-zA-Z]{1,})\s*([A-Z][a-zA-Z]{1,})\s*([A-Z][a-zA-Z]{1,})\s*(BSIT|BLIS|BSCS|BSIS)\s*(\d)([A-H])\s*(\d+)/', $line, $matches)) {
                $data = [
                    'student_number' => $matches[1],
                    'first_name' => $matches[2],
                    'middle_name' => $matches[3],
                    'last_name' => $matches[4],
                    'program' => $matches[5],
                    'year' => $matches[6],
                    'section' => $matches[7],
                    'pc_number' => $matches[8],
                ];

                try {
                    Student::create($data);
                    Log::info("Student created successfully: " . json_encode($data));
                } catch (\Exception $e) {
                    $errors[] = "Failed to create student with student number {$matches[1]}: {$e->getMessage()}";
                    Log::error("Failed to create student: " . json_encode($data) . " Error: " . $e->getMessage());
                }
            } else {
                $errors[] = "Skipped line, not enough data parts: " . $line;
                Log::warning("Skipped line, not enough data parts: " . json_encode(['line' => $line]));
            }
        }

        if (!empty($errors)) {
            return redirect()->route('students.index')->with('errors', $errors);
        }

        return redirect()->route('students.index')->with('success', 'Students imported successfully.');
    }
}
