<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Schedule;
use App\Models\RFID;
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
        $schedules = $currentUser->schedules;

        Log::info('Authenticated User:', [
            'user_id' => $currentUser->id,
            'user_name' => $currentUser->firstname . ' ' . $currentUser->lastname,
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

        $studentsQuery = Student::query();

        if ($schedules->isNotEmpty()) {
            $studentsQuery->where(function($query) use ($schedules) {
                foreach ($schedules as $schedule) {
                    $query->orWhere(function($q) use ($schedule) {
                        $q->where('program', $schedule->program)
                          ->where('year', $schedule->year)
                          ->where('section', $schedule->section);
                    });
                }
            });
        }

        $studentsQuery->when($search, function ($query, $search) {
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
        ->orderBy('program')
        ->orderBy('year')
        ->orderBy('section')
        ->orderBy('pc_number');

        $students = $studentsQuery->paginate(10);

        $allSchedules = Schedule::all();

        return view('faculty.students.index', compact('students', 'search', 'gender', 'program', 'year', 'section', 'scheduleId', 'allSchedules'));
    }

    public function create()
    {
        return view('faculty.students.create');
    }


//STORE

public function store(Request $request)
{
    \Log::info('Student Data:', $request->all());

    $validatedData = $request->validate([
        'student_number' => 'required|unique:students',
        'firstname' => 'required|regex:/^[a-zA-Z\s]{2,}$/',
        'middlename' => 'nullable|regex:/^[a-zA-Z\s]{2,}$/',
        'lastname' => 'required|regex:/^[a-zA-Z\s]{2,}$/',
        'program' => 'required|in:BSIT,BLIS,BSCS,BSIS',
        'year' => 'required|in:1,2,3,4',
        'section' => 'required|in:A,B,C,D,E,F,G,H',
        'gender' => 'required|in:male,female',
        'pc_number' => 'required|integer',
        'rfid' => 'nullable|string'
    ]);

    \Log::info('Validated Data:', $validatedData);

    $firstName = ucwords(strtolower($request->input('firstname')));
    $middleName = $request->input('middlename') ? ucwords(strtolower($request->input('middlename'))) : null;
    $lastName = ucwords(strtolower($request->input('lastname')));

    $rfidCode = $request->input('rfid');
    if ($rfidCode) {
        $rfidCode = strtoupper(str_replace(' ', '', $rfidCode));
    }

    if ($rfidCode) {
        $existingRfid = RFID::whereRaw('REPLACE(UPPER(rfid_code), " ", "") = ?', [$rfidCode])->first();
        if ($existingRfid) {
            return redirect()->back()->withErrors(['rfid' => 'This RFID is already assigned to another user.']);
        }
    }

    $sectionCount = Student::where('program', $request->input('program'))
                            ->where('year', $request->input('year'))
                            ->where('section', $request->input('section'))
                            ->count();

    if ($sectionCount >= 30) {
        return redirect()->back()->withErrors(['section' => 'This section already has the maximum number of students (30).']);
    }

    $duplicatePC = Student::where('program', $request->input('program'))
                            ->where('year', $request->input('year'))
                            ->where('section', $request->input('section'))
                            ->where('pc_number', $request->input('pc_number'))
                            ->exists();

    if ($duplicatePC) {
        return redirect()->back()->withErrors(['pc_number' => 'This PC number is already assigned to another student in the same section.']);
    }

    try {
        \DB::beginTransaction();

        $rfidId = null;
        if ($rfidCode) {
            $rfid = RFID::create(['rfid_code' => $rfidCode]);
            $rfidId = $rfid->id;
        }

        Student::create([
            'student_number' => $request->input('student_number'),
            'first_name' => $firstName,
            'middle_name' => $middleName,
            'last_name' => $lastName,
            'program' => $request->input('program'),
            'year' => $request->input('year'),
            'section' => $request->input('section'),
            'gender' => $request->input('gender'),
            'pc_number' => $request->input('pc_number'),
            'rfid_id' => $rfidId
        ]);

        \DB::commit();
        \Log::info('Student created successfully.');
    } catch (\Exception $e) {
        \DB::rollBack();
        \Log::error('Failed to create student:', ['error' => $e->getMessage()]);
        return redirect()->back()->withErrors(['error' => 'Failed to create student.']);
    }

    return redirect()->route('students.index')->with('success', 'Student created successfully.');
}







    
    
    
    
    //update

    public function update(Request $request, Student $student)
    {
        \Log::info('Student Data:', $request->all());
    
        $validatedData = $request->validate([
            'student_number' => 'required|unique:students,student_number,' . $student->id,
            'firstname' => 'required|regex:/^[a-zA-Z\s]{2,}$/',
            'middlename' => 'nullable|regex:/^[a-zA-Z\s]{2,}$/',
            'lastname' => 'required|regex:/^[a-zA-Z\s]{2,}$/',
            'program' => 'required|in:BSIT,BLIS,BSCS,BSIS',
            'year' => 'required|in:1,2,3,4',
            'section' => 'required|in:A,B,C,D,E,F,G,H',
            'gender' => 'required|in:male,female',
            'pc_number' => 'required|integer',
            'rfid' => 'nullable|string'
        ]);
    
        \Log::info('Validated Data:', $validatedData);
    
        $firstName = ucwords(strtolower($request->input('firstname')));
        $middleName = $request->input('middlename') ? ucwords(strtolower($request->input('middlename'))) : null;
        $lastName = ucwords(strtolower($request->input('lastname')));
    
        $sectionCount = Student::where('program', $request->input('program'))
                                ->where('year', $request->input('year'))
                                ->where('section', $request->input('section'))
                                ->where('id', '!=', $student->id)
                                ->count();
    
        if ($sectionCount >= 30) {
            return redirect()->back()->withErrors(['section' => 'This section already has the maximum number of students (30).']);
        }
    
        $duplicatePC = Student::where('program', $request->input('program'))
                                ->where('year', $request->input('year'))
                                ->where('section', $request->input('section'))
                                ->where('pc_number', $request->input('pc_number'))
                                ->where('id', '!=', $student->id)
                                ->exists();
    
        if ($duplicatePC) {
            return redirect()->back()->withErrors(['pc_number' => 'This PC number is already assigned to another student in the same section.']);
        }
    
        $newRfidCode = str_replace(' ', '', $request->input('rfid'));
    
        if ($newRfidCode) {
            $existingRfid = RFID::whereRaw('REPLACE(rfid_code, " ", "") = ?', [$newRfidCode])->first();
    
            if ($existingRfid && $existingRfid->id !== $student->rfid_id) {
                return redirect()->back()->withErrors(['rfid' => 'The RFID code is already assigned to another student.']);
            }
        }
    
        try {
            \DB::beginTransaction();
    
            $rfidId = null;
            if ($newRfidCode) {
                $rfid = RFID::whereRaw('REPLACE(rfid_code, " ", "") = ?', [$newRfidCode])->first();
    
                if (!$rfid) {
                    $rfid = RFID::create(['rfid_code' => $newRfidCode]);
                }
                $rfidId = $rfid->id;
            }
    
            $student->update([
                'student_number' => $request->input('student_number'),
                'first_name' => $firstName,
                'middle_name' => $middleName,
                'last_name' => $lastName,
                'program' => $request->input('program'),
                'year' => $request->input('year'),
                'section' => $request->input('section'),
                'gender' => $request->input('gender'),
                'pc_number' => $request->input('pc_number'),
                'rfid_id' => $rfidId
            ]);
    
            if ($student->rfid_id) {
                $oldRfid = RFID::find($student->rfid_id);
    
                if ($oldRfid && !Student::where('rfid_id', $oldRfid->id)->exists()) {
                    $oldRfid->delete();
                }
            }
    
            \DB::commit();
            \Log::info('Student updated successfully.');
        } catch (\Exception $e) {
            \DB::rollBack();
            \Log::error('Failed to update student:', ['error' => $e->getMessage()]);
            return redirect()->back()->withErrors(['error' => 'Failed to update student.']);
        }
    
        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }
    
    
    

    public function edit(Student $student)
    {
        return view('faculty.students.edit', compact('student'));
    }

    public function destroy(Student $student)
    {
        if ($student->rfid) {
            $student->rfid->delete();
        }
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
                continue;
            }
    

            if (!preg_match('/^[\p{L}\'\- ]+$/u', $row[1]) || 
                !preg_match('/^[\p{L}\'\- ]+$/u', $row[2]) || 
                !preg_match('/^[\p{L}\'\- ]+$/u', $row[3])) {
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

        $lines = explode("\n", $text);
        Log::info("PDF Lines: " . json_encode($lines));

        foreach ($lines as $line) {
            if (preg_match('/(\d{6})([A-Z][a-zA-Z]{1,})\s*([A-Z][a-zA-Z]{1,})\s*([A-Z][a-zA-Z]{1,})\s*(BSIT|BLIS|BSCS|BSIS)\s*(\d)([A-H])\s*(\d+)/', $line, $matches)) {
                $data = [
                    'student_number' => $matches[1],
                    'firstname' => $matches[2],
                    'middlename' => $matches[3],
                    'lastname' => $matches[4],
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
