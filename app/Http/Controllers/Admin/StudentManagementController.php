<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Schedule;
use App\Models\StudentAttendance;
use App\Models\RFID;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpSpreadsheet\IOFactory;




class StudentManagementController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $gender = $request->input('gender');
        $program = $request->input('program');
        $year = $request->input('year');
        $section = $request->input('section');
    
        $studentsQuery = Student::query();
    
        $studentsQuery->when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
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
    
        return view('admin.students.index', compact('students', 'search', 'gender', 'program', 'year', 'section'));
    }
    
    

    #create

    public function create(Request $request)
    {
        $clientIp = $request->getClientIp();
        return view('admin.students.create', ['clientIp' => $clientIp]);
    }

    #store

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
            'pc_number' => 'required|integer|between:1,30',
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

        return redirect()->route('admin.students.index')->with('success', 'Student created successfully.');
    }

    #update

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
            'pc_number' => 'required|integer|between:1,30',
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

        return redirect()->route('admin.students.index')->with('success', 'Student updated successfully.');
    }

    #edit

    public function edit(Student $student, Request $request)
    {
        $clientIp = $request->getClientIp();
        return view('admin.students.edit', compact('student'), ['clientIp' => $clientIp]);
    }

    #delete

    public function destroy(Student $student)
    {
        if ($student->rfid_id) {
            $rfid = RFID::find($student->rfid_id);
            if ($rfid && !Student::where('rfid_id', $rfid->id)->exists()) {
                $rfid->delete();
            }
        }
        
        // Set foreign key in student_attendances to null
        $student->attendances()->update(['student_id' => null]);
        
        // Now delete the student
        $student->delete();
    
        return redirect()->route('admin.students.index')->with('success', 'Student deleted successfully.');
    }
    


    #import as excel

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
        $successCount = 0; // Track successful imports
    
        foreach ($rows as $index => $row) {
            if ($index == 0) {
                continue; 
            }
    
            $studentNumber = trim($row[0]); 
            $firstName = ucwords(strtolower(trim($row[1]))); 
            $middleName = !empty($row[2]) ? ucwords(strtolower(trim($row[2]))) : null;
            $lastName = ucwords(strtolower(trim($row[3]))); 
            $program = trim($row[4]); 
            $yearAndSection = trim($row[5]); 
            $pcNumber = trim($row[6]); 
            $rfidCode = strtoupper(str_replace(' ', '', $row[7]));
            $gender = isset($row[8]) ? trim($row[8]) : null; 
    
            // Validate middle name
            if (!empty($middleName) && preg_match('/^([A-Z]{1,2}\.?|Ll\.?|[A-Z]\.?|[A-Z])$/i', $middleName)) {
                $errors[] = "Student {$studentNumber}: Middle name cannot be a single initial or 'Ll' (e.g., A., B., Ll, Ll.).";
                continue;
            }
    
            // Validate RFID Code
            if (!$rfidCode) {
                $errors[] = "Student {$studentNumber}: RFID Code is required.";
                continue;
            }
    
            // Check if RFID is already assigned
            $existingRfid = RFID::whereRaw('REPLACE(UPPER(rfid_code), " ", "") = ?', [$rfidCode])->first();
            if ($existingRfid) {
                $errors[] = "Student {$studentNumber}: This RFID is already assigned to another user.";
                continue;
            }
    
            // Check if the section already has 30 students
            $sectionCount = Student::where('program', $program)
                ->where('year', substr($yearAndSection, 0, 1))
                ->where('section', substr($yearAndSection, 1))
                ->count();
    
            if ($sectionCount >= 30) {
                $errors[] = "Student {$studentNumber}: Section already has the maximum number of students (30).";
                continue;
            }
    
            // Check if PC number is already assigned within the same section
            $duplicatePC = Student::where('program', $program)
                ->where('year', substr($yearAndSection, 0, 1))
                ->where('section', substr($yearAndSection, 1))
                ->where('pc_number', $pcNumber)
                ->exists();
    
            if ($duplicatePC) {
                $errors[] = "Student {$studentNumber}: This PC number is already assigned to another student in the same section.";
                continue;
            }
    
            try {
                \DB::beginTransaction();
    
                $rfid = RFID::create(['rfid_code' => $rfidCode]);
                $rfidId = $rfid->id;
    
                Student::create([
                    'student_number' => $studentNumber,
                    'first_name' => $firstName,
                    'middle_name' => $middleName,
                    'last_name' => $lastName,
                    'program' => $program,
                    'year' => substr($yearAndSection, 0, 1),
                    'section' => substr($yearAndSection, 1),
                    'gender' => $gender,  
                    'pc_number' => $pcNumber,
                    'rfid_id' => $rfidId
                ]);
    
                \DB::commit();
                $successCount++;
            } catch (\Exception $e) {
                \DB::rollBack();
                $errors[] = "Student {$studentNumber}: Failed to create student: {$e->getMessage()}";
                Log::error("Failed to create student: " . json_encode($row) . " Error: " . $e->getMessage());
            }
        }
    
        // Redirect with errors or success
        if (!empty($errors)) {
            return redirect()->route('students.index')->with('errors', $errors);
        }
        
    
        if ($successCount > 0) {
            return redirect()->route('students.index')->with('success', "{$successCount} students imported successfully.");
        }
    
        return redirect()->route('students.index')->with('errors', ['No students were imported.']);
    }
    
    
    
    

    #import as pdf

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
            return redirect()->route('admin.students.index')->with('errors', $errors);
        }

        return redirect()->route('admin.students.index')->with('success', 'Students imported successfully.');
    }









    // public function edit(Student $student)
    // {
    //     return view('admin.students.edit', compact('student'));
    // }

    // public function update(Request $request, Student $student)
    // {
    //     $data = $request->validate([
    //         'student_number' => 'required|string|unique:students,student_number,' . $student->id,
    //         'first_name' => 'required|string',
    //         'middle_name' => 'nullable|string',
    //         'last_name' => 'required|string',
    //         'program' => 'required|in:BSIT,BLIS,BSCS,BSIS',
    //         'year_and_section' => 'required|string',
    //         'gender' => 'required|in:male,female',
    //         'pc_number' => 'required|integer',
    //     ]);

    //     $student->update($data);

    //     return redirect()->route('admin.students.index')->with('success', 'Student updated successfully');
    // }

    // public function destroy(Student $student)
    // {
    //     $student->delete();

    //     return redirect()->route('admin.students.index')->with('success', 'Student deleted successfully');
    // }
}
