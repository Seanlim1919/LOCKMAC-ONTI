<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\RFID;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    public function index(Request $request) 
    {
        // Get the current authenticated user (faculty)
        $currentUser = auth()->user();
    
        // Retrieve the schedules associated with the current user
        $schedules = $currentUser->schedules;
    
        // Get the IDs of students based on the schedules
        $studentIds = Student::whereIn('program', $schedules->pluck('program'))
                             ->whereIn('year', $schedules->pluck('year'))
                             ->whereIn('section', $schedules->pluck('section'))
                             ->pluck('id');
    
        $students = Student::query()->whereIn('id', $studentIds);
    
        // Apply filters based on request inputs
        if ($request->input('search') || $request->input('gender') || $request->input('program') || $request->input('year') || $request->input('section')) {
            $students->when($request->input('search'), fn($query, $search) => $query->where(function ($q) use ($search) {
                $q->where('first_name', 'LIKE', "%{$search}%")
                  ->orWhere('last_name', 'LIKE', "%{$search}%")
                  ->orWhere('student_number', 'LIKE', "%{$search}%")
                  ->orWhere('program', 'LIKE', "%{$search}%")
                  ->orWhere('year', 'LIKE', "%{$search}%")
                  ->orWhere('section', 'LIKE', "%{$search}%");
            }))
            ->when($request->input('gender'), fn($query, $gender) => $query->where('gender', $gender))
            ->when($request->input('program'), fn($query, $program) => $query->where('program', $program))
            ->when($request->input('year'), fn($query, $year) => $query->where('year', $year))
            ->when($request->input('section'), fn($query, $section) => $query->where('section', $section));
        }
    
        $students = $students->orderBy('program')
                             ->orderBy('year')
                             ->orderBy('section')
                             ->paginate(10);
    
        return view('faculty.students.index', compact('students'));
    }
    
    

    public function create()
    {
        $clientIp = request()->ip(); // Get the client IP address
        $currentCount = Student::count();
        $assignedPCNumbers = $currentCount < 30 ? $currentCount + 1 : 'No Assigned PC';
    
        return view('faculty.students.create', compact('assignedPCNumbers', 'clientIp'));
    }
    
    

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'student_number' => 'required|unique:students',
            'firstname' => 'required|regex:/^[a-zA-Z\s]+$/',
            'middlename' => 'nullable|regex:/^[a-zA-Z\s]*$/',
            'lastname' => 'required|regex:/^[a-zA-Z\s]+$/',
            'program' => 'required|in:BSIT,BLIS,BSCS,BSIS',
            'year' => 'required|in:1,2,3,4',
            'section' => 'required|in:A,B,C,D,E,F,G,H',
            'gender' => 'required|in:male,female',
            'pc_number' => 'required|integer|between:1,30',
            'rfid' => 'nullable|string'
        ]);

        // Normalize names
        $validatedData['first_name'] = ucwords(strtolower($validatedData['firstname']));
        $validatedData['middle_name'] = isset($validatedData['middlename']) ? ucwords(strtolower($validatedData['middlename'])) : null;
        $validatedData['last_name'] = ucwords(strtolower($validatedData['lastname']));

        // Handle RFID logic
        $rfidId = null;
        if ($request->input('rfid')) {
            $rfidCode = strtoupper(str_replace(' ', '', $request->input('rfid')));
            $existingRfid = RFID::where('rfid_code', $rfidCode)->first();
            if ($existingRfid) {
                return redirect()->back()->withErrors(['rfid' => 'This RFID is already assigned to another user.']);
            }
            $rfid = RFID::create(['rfid_code' => $rfidCode]);
            $rfidId = $rfid->id;
        }

        // Check for section limits and duplicate PC numbers
        $this->checkSectionLimits($validatedData, null);

        try {
            Student::create(array_merge($validatedData, ['rfid_id' => $rfidId]));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to create student.']);
        }

        return redirect()->route('students.index')->with('success', 'Student created successfully.');
    }

    public function edit(Student $student)
    {
        return view('faculty.students.edit', compact('student'));
    }

    public function update(Request $request, Student $student)
    {
        $validatedData = $request->validate([
            'student_number' => 'required|unique:students,student_number,' . $student->id,
            'firstname' => 'required|regex:/^[a-zA-Z\s]+$/',
            'middlename' => 'nullable|string|regex:/^[a-zA-Z ]*$/',
            'lastname' => 'required|regex:/^[a-zA-Z\s]+$/',
            'program' => 'required|in:BSIT,BLIS,BSCS,BSIS',
            'year' => 'required|in:1,2,3,4',
            'section' => 'required|in:A,B,C,D,E,F,G,H',
            'gender' => 'required|in:male,female',
            'pc_number' => 'required|integer|between:1,30',
            'rfid' => 'nullable|string'
        ]);

        // Normalize names
        $validatedData['first_name'] = ucwords(strtolower($validatedData['firstname']));
        $validatedData['middle_name'] = isset($validatedData['middlename']) ? ucwords(strtolower($validatedData['middlename'])) : null;
        $validatedData['last_name'] = ucwords(strtolower($validatedData['lastname']));

        // Handle RFID logic
        $newRfidId = null;
        if ($request->input('rfid')) {
            $rfidCode = strtoupper(str_replace(' ', '', $request->input('rfid')));
            $existingRfid = RFID::where('rfid_code', $rfidCode)->first();
            if ($existingRfid && $existingRfid->id !== $student->rfid_id) {
                return redirect()->back()->withErrors(['rfid' => 'The RFID code is already assigned to another student.']);
            }
            if (!$existingRfid) {
                $rfid = RFID::create(['rfid_code' => $rfidCode]);
                $newRfidId = $rfid->id;
            } else {
                $newRfidId = $existingRfid->id;
            }
        }

        // Check for section limits and duplicate PC numbers
        $this->checkSectionLimits($validatedData, $student->id);

        try {
            $student->update(array_merge($validatedData, ['rfid_id' => $newRfidId]));
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to update student.']);
        }

        return redirect()->route('students.index')->with('success', 'Student updated successfully.');
    }

    public function destroy(Student $student)
    {
        if ($student->rfid_id) {
            $rfid = RFID::find($student->rfid_id);
            if ($rfid && !Student::where('rfid_id', $rfid->id)->exists()) {
                $rfid->delete();
            }
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
    
            $studentNumber = trim($row[0]); 
            $firstName = ucwords(strtolower(trim($row[1]))); 
            $middleName = !empty($row[2]) ? ucwords(strtolower(trim($row[2]))) : null;
            $lastName = ucwords(strtolower(trim($row[3]))); 
            $program = trim($row[4]); 
            $yearAndSection = trim($row[5]); 
            $pcNumber = trim($row[6]); 
            $rfidCode = strtoupper(str_replace(' ', '', $row[7]));
            $gender = isset($row[8]) ? trim($row[8]) : null; 
    
            // Validate middle name - ensure it's not an initial (e.g., "A.", "B", "Ll", "Ll.")
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
    
            // Check if the section already has 50 students
            $sectionCount = Student::where('program', $program)
                ->where('year', substr($yearAndSection, 0, 1))
                ->where('section', substr($yearAndSection, 1))
                ->count();
    
            if ($sectionCount >= 50) {
                $errors[] = "Student {$studentNumber}: Section already has the maximum number of students (50).";
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
            } catch (\Exception $e) {
                \DB::rollBack();
                $errors[] = "Student {$studentNumber}: Failed to create student: {$e->getMessage()}";
                Log::error("Failed to create student: " . json_encode($row) . " Error: " . $e->getMessage());
            }
        }
    
        if (!empty($errors)) {
            return redirect()->route('students.index')->with('errors', $errors);
        }
    
        return redirect()->route('students.index')->with('success', 'Students imported successfully.');
    }

    public function importPDF(Request $request)
    {
        $request->validate(['file' => 'required|mimes:pdf']);
        $path = $request->file('file')->getRealPath();
        $parser = new Parser();
        $pdf = $parser->parseFile($path);
        $text = $pdf->getText();
        $lines = explode("\n", $text);
        $errors = [];

        foreach ($lines as $line) {
            // Process each line for student data
            // Validate and create students
        }

        if (!empty($errors)) {
            return redirect()->route('students.index')->with('errors', $errors);
        }

        return redirect()->route('students.index')->with('success', 'Students imported successfully.');
    }

    private function checkSectionLimits(array $data, ?int $studentId)
    {
        $sectionCount = Student::where('program', $data['program'])
            ->where('year', $data['year'])
            ->where('section', $data['section'])
            ->when($studentId, fn($query) => $query->where('id', '!=', $studentId))
            ->count();

        if ($sectionCount >= 50) {
            throw ValidationException::withMessages(['section' => 'This section already has the maximum number of students (50).']);
        }

        $duplicatePC = Student::where('program', $data['program'])
            ->where('year', $data['year'])
            ->where('section', $data['section'])
            ->where('pc_number', $data['pc_number'])
            ->when($studentId, fn($query) => $query->where('id', '!=', $studentId))
            ->exists();

        if ($duplicatePC) {
            throw ValidationException::withMessages(['pc_number' => 'PC number already exists in this section.']);
        }
    }
}
