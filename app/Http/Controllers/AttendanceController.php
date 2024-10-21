<?php

namespace App\Http\Controllers;

use App\Exports\FacultyAttendanceExport;
use App\Exports\StudentAttendanceExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Attendance;
use App\Models\StudentAttendance;
use App\Models\Course;
use App\Models\Student;
use App\Models\Schedule;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon; 


class AttendanceController extends Controller
{


    public function showFacultyAttendance(Request $request)
    {
        $query = Attendance::whereHas('user', function($query) {
            $query->where('role', 'faculty')->orWhere('role', 'admin');
        })
        ->with(['user', 'schedule.course']) // Load related schedule and course
        ->orderBy('entered_at', 'asc');

        if ($request->filled('month')) {
            $query->whereMonth('entered_at', $request->month);
        }

        if ($request->filled('day')) {
            $query->whereDay('entered_at', $request->day);
        }

        if ($request->filled('year')) {
            $query->whereYear('entered_at', $request->year);
        }

        $facultyAttendances = $query->paginate(10);

        return view('admin.attendance', compact('facultyAttendances'));
    }

    public function exportFacultyAttendancePdf(Request $request)
    {
        // Get start and end dates from the request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
    
        // Fetch attendances while filtering by date range if provided
        $facultyAttendances = Attendance::with(['user', 'schedule.course'])
            ->when($startDate, function ($query) use ($startDate) {
                return $query->where('entered_at', '>=', Carbon::parse($startDate)->startOfDay());
            })
            ->when($endDate, function ($query) use ($endDate) {
                return $query->where('entered_at', '<=', Carbon::parse($endDate)->endOfDay());
            })
            ->get()
            ->groupBy(function ($attendance) {
                return $attendance->user->id . '-' . ($attendance->schedule->course->id ?? 'N/A');
            });
    
        $data = [];
        foreach ($facultyAttendances as $group) {
            $firstAttendance = $group->sortBy('entered_at')->first();
            $lastAttendance = $group->sortByDesc('exited_at')->first();
    
            $enteredAt = Carbon::parse($firstAttendance->entered_at);
            $schedule = $firstAttendance->schedule;
            $courseDetails = $schedule ? $schedule->course : null;
    
            $isAdmin = $firstAttendance->user->role == 'admin';
    
            $data[] = [
                'Faculty Name' => $firstAttendance->user ? 
                    ($firstAttendance->user->first_name . ' ' . $firstAttendance->user->last_name . ($isAdmin ? ' (Admin)' : '')) : 
                    'N/A',
                'Course' => $isAdmin ? 'N/A' : ($courseDetails ? $courseDetails->course_code : 'N/A'),
                'Program' => $isAdmin ? 'N/A' : ($schedule ? $schedule->program : 'N/A'),
                'Year' => $isAdmin ? 'N/A' : ($schedule ? $schedule->year : 'N/A'),
                'Section' => $isAdmin ? 'N/A' : ($schedule ? $schedule->section : 'N/A'),
                'Date' => $enteredAt->format('Y-m-d'),
                'Time In' => $enteredAt->format('g:i A'),
                'Time Out' => $lastAttendance->exited_at ? Carbon::parse($lastAttendance->exited_at)->format('g:i A') : null,
            ];
        }
    
        // Load the data into the PDF view, including start and end dates
        $pdf = Pdf::loadView('admin.pdf', [
            'facultyAttendances' => $data,
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);
    
        return $pdf->stream('faculty_attendance.pdf');
    }
    
    

    
    

    public function showStudentAttendance(Request $request)
    {
        $date = $request->input('date');
        $section = $request->input('section');
        $course = $request->input('course');
        $program = $request->input('program');  
        $year = $request->input('year');
        
        $currentUser = auth()->user();
        Log::info('Authenticated User:', [
            'user_id' => $currentUser->id,
            'user_name' => $currentUser->firstname . ' ' . $currentUser->lastname,
        ]);
        
        // Retrieve the schedules associated with the current user
        $schedules = $currentUser->schedules;
    
        // Fetch student IDs based on the schedules
        $studentIds = Student::whereIn('program', $schedules->pluck('program'))
                             ->whereIn('year', $schedules->pluck('year'))
                             ->whereIn('section', $schedules->pluck('section'))
                             ->pluck('id');
    
        // Build the query to get attendance records
        $query = StudentAttendance::with(['student', 'schedule.course'])
            ->where('faculty_id', $currentUser->id)
            ->whereIn('student_id', $studentIds) // Filter by student IDs
            ->whereHas('schedule', function ($q) use ($schedules, $course, $program, $year, $section) {
                $q->whereIn('id', $schedules->pluck('id'))
                    ->when($course, function ($q) use ($course) {
                        $q->where('course_id', $course);
                    })
                    ->when($program, function ($q) use ($program) {
                        $q->where('program', $program);
                    })
                    ->when($year, function ($q) use ($year) {
                        $q->where('year', $year);
                    })
                    ->when($section, function ($q) use ($section) {
                        $q->where('section', $section);
                    });
            });
    
        // Apply date filter if provided
        if ($date) {    
            $query->whereDate('entered_at', $date);
        }
        
        // Execute the query and get the results, ordering by entered_at descending
        $studentAttendances = $query->orderBy('entered_at', 'desc')->get();
        
        // Prepare additional data for the view
        $sections = Student::whereIn('id', $studentAttendances->pluck('student_id'))->distinct()->pluck('section');
        $courses = Course::whereIn('id', $studentAttendances->pluck('course_id'))->distinct()->get(); 
        $allSchedules = Schedule::distinct()->pluck('program')->toArray();
        $years = Schedule::distinct()->pluck('year')->toArray();
    
        return view('faculty.attendance', compact('studentAttendances', 'date', 'section', 'course', 'program', 'years', 'sections', 'courses', 'allSchedules'));
    }
    
    
    
    
    
    
    #for filter to ha
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $course = $request->input('course');
        $program = $request->input('program');
        $year = $request->input('year');
        $section = $request->input('section');
        $search = $request->input('search');
        
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
                    'section' => $schedule->section,
                    'schedule_id' => $schedule->id
                ]);
            }
        } else {
            Log::info('Current Schedule:', ['message' => 'No schedule found for the user.']);
        }
    
        $query = StudentAttendance::with(['student', 'schedule.course'])
            ->where('faculty_id', $currentUser->id)
            ->whereHas('student', function ($q) use ($schedules) {
                $q->where(function ($query) use ($schedules) {
                    foreach ($schedules as $schedule) {
                        $query->orWhere(function ($q) use ($schedule) {
                            $q->where('program', $schedule->program)
                              ->where('year', $schedule->year)
                              ->where('section', $schedule->section);
                        });
                    }
                });
            })
            ->when($startDate && $endDate, function ($q) use ($startDate, $endDate) {
                $q->whereBetween('entered_at', [$startDate, $endDate]);
            })
            ->when($course, function ($q) use ($course) {
                $q->whereHas('schedule', function ($q) use ($course) {
                    $q->where('course_id', $course);
                });
            })
            ->when($program, function ($q) use ($program) {
                $q->whereHas('student', function ($q) use ($program) {
                    $q->where('program', $program);
                });
            })
            ->when($year, function ($q) use ($year) {
                $q->whereHas('student', function ($q) use ($year) {
                    $q->where('year', $year);
                });
            })
            ->when($section, function ($q) use ($section) {
                $q->whereHas('student', function ($q) use ($section) {
                    $q->where('section', $section);
                });
            })
            ->when($search, function ($q) use ($search) {
                $q->whereHas('student', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                      ->orWhere('last_name', 'like', "%{$search}%")
                      ->orWhere('student_number', 'like', "%{$search}%");
                });
            })
            ->orderBy('entered_at', 'asc');
    
        $studentAttendances = $query->get();
        
        $courses = Course::whereIn('id', $studentAttendances->pluck('course_id'))->distinct()->get(); 
        $sections = Student::distinct()->pluck('section');
        $allSchedules = Schedule::all();
        $years = Schedule::distinct()->pluck('year')->toArray();
    
        return view('faculty.attendance', compact('studentAttendances', 'courses', 'sections', 'search', 'startDate', 'endDate', 'section', 'course', 'allSchedules', 'years'));
    }
    
    
    
    public function export(Request $request)
    {
        $validatedData = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date', // Allows end_date to be the same as start_date
            'year' => 'required|integer',
            'section' => 'required|string',
            'course' => 'required|exists:courses,id',
        ]);
    
        $startDate = $validatedData['start_date'];
        $endDate = $validatedData['end_date'];
        $year = $validatedData['year'];
        $section = $validatedData['section'];
        $courseId = $validatedData['course'];
    
        $currentUser = auth()->user();
        $schedules = $currentUser->schedules;
    
        // Fetch the course object
        $course = Course::find($courseId);
    
        // Fetch attendance records within the specified date range
        $studentAttendances = StudentAttendance::with(['student', 'schedule.course'])
            ->where('faculty_id', $currentUser->id)
            ->whereBetween('entered_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']) // Include the full date range
            ->whereHas('schedule', function ($q) use ($schedules) {
                $q->whereIn('id', $schedules->pluck('id'));
            })
            ->whereHas('student', function ($q) use ($year, $section) {
                $q->where('year', $year)
                  ->where('section', $section);
            })
            ->whereHas('course', function ($q) use ($courseId) {
                $q->where('id', $courseId);
            })
            ->get();
    
        // Check if any records were found
        if ($studentAttendances->isEmpty()) {
            return back()->with('error', 'No attendance records found for the selected criteria.');
        }
    
        // Get all students for the summary
        $filteredStudents = Student::where('year', $year)
            ->where('section', $section)
            ->where('program', $schedules->first()->program)
            ->pluck('id')
            ->toArray();
    
        // Group attendance by date
        $attendanceSummary = $studentAttendances->groupBy(function ($attendance) {
            return $attendance->entered_at->format('Y-m-d');
        });
    
        // Prepare summary data
        $summaries = [];
        foreach ($attendanceSummary as $date => $attendances) {
            $presentStudentIds = $attendances->pluck('student_id')->toArray();
            $absentStudentIds = array_diff($filteredStudents, $presentStudentIds);
            $absentStudents = Student::whereIn('id', $absentStudentIds)->get();
    
            $summaries[$date] = [
                'totalPresent' => $attendances->count(),
                'totalAbsent' => $absentStudents->count(),
                'absentStudents' => $absentStudents,
                'attendances' => $attendances // Include attendances for this date
            ];
        }
    
        // Load the view for the PDF
        $pdf = PDF::loadView('faculty.student_attendance_export', compact(
            'summaries',
            'startDate',
            'endDate',
            'year',
            'section',
            'courseId',
            'course' // Now correctly passed as an object
        ))->setPaper('a4', 'landscape');
    
        return $pdf->stream('student_attendance.pdf');
    }
    
    
    

    // public function export(Request $request)
    // {
    //     $date = $request->input('date');
    //     $course = $request->input('course');
    //     $program = $request->input('program');
    //     $year = $request->input('year');
    //     $section = $request->input('section');
    
    //     $currentUser = auth()->user();
    //     $schedules = $currentUser->schedules;
    
    //     $query = StudentAttendance::with(['student', 'schedule.course'])
    //         ->where('faculty_id', $currentUser->id)
    //         ->whereHas('schedule', function($q) use ($schedules) {
    //             $q->whereIn('id', $schedules->pluck('id'));
    //         });
    
    //     if ($date) {
    //         $query->whereDate('entered_at', $date);
    //     }
    
    //     if ($section) {
    //         $query->whereHas('student', function ($q) use ($section) {
    //             $q->where('section', $section);
    //         });
    //     }
    
    //     if ($course) {
    //         $query->whereHas('schedule', function ($q) use ($course) {
    //             $q->where('course_id', $course);
    //         });
    //     }
    
    //     if ($program) {
    //         $query->whereHas('student', function ($q) use ($program) {
    //             $q->where('program', $program);
    //         });
    //     }
    
    //     if ($year) {
    //         $query->whereHas('student', function ($q) use ($year) {
    //             $q->where('year', $year);
    //         });
    //     }
    
    //     $studentAttendances = $query->get();
    
    //     $attendanceSummary = $studentAttendances->groupBy(function($attendance) {
    //         return $attendance->entered_at->format('Y-m-d');
    //     });
    
    //     $summaries = [];
    //     $allStudentIds = Student::pluck('id')->toArray();
    
    //     foreach ($attendanceSummary as $date => $attendances) {
    //         $presentStudentIds = $attendances->pluck('student_id')->toArray();
    //         $absentStudentIds = array_diff($allStudentIds, $presentStudentIds);
    //         $absentStudents = Student::whereIn('id', $absentStudentIds)->get();
    
    //         $summaries[$date] = [
    //             'totalPresent' => $attendances->count(),
    //             'totalAbsent' => $absentStudents->count(),
    //             'absentStudents' => $absentStudents,
    //         ];
    //     }
    
    //     foreach ($studentAttendances as $attendance) {
    //         $attendance->formatted_entered_at = \Carbon\Carbon::parse($attendance->entered_at)->format('m/d/Y g:i A'); 
    //         $attendance->formatted_exited_at = $attendance->exited_at ? \Carbon\Carbon::parse($attendance->exited_at)->format('m/d/Y g:i A') : 'N/A';
    //     }
    
    //     $pdf = Pdf::loadView('faculty.student_attendance_export', compact(
    //         'studentAttendances',
    //         'summaries'
    //     ))->setPaper('a4', 'landscape');
    
    //     return $pdf->stream('student_attendance.pdf');
    // }
    
    
    
    

    public function exportLogbookPdf(Request $request)
    {
        $date = $request->input('date');
        $section = $request->input('section');
        $course = $request->input('course');
        $program = $request->input('program');
        $year = $request->input('year');
    
        $currentUser = auth()->user();
        $schedules = $currentUser->schedules;
    
        $query = StudentAttendance::with(['student', 'course'])
            ->where('faculty_id', $currentUser->id)
            ->whereHas('schedule', function($q) use ($schedules) {
                $q->whereIn('id', $schedules->pluck('id'));
            });
    
        if ($date) {
            $query->whereDate('entered_at', $date);
        }
    
        if ($section) {
            $query->whereHas('student', function ($q) use ($section) {
                $q->where('section', $section);
            });
        }
    
        if ($course) {
            $query->whereHas('schedule', function ($q) use ($course) {
                $q->where('course_id', $course);
            });
        }
    
        if ($program) {
            $query->whereHas('student', function ($q) use ($program) {
                $q->where('program', $program);
            });
        }
    
        if ($year) {
            $query->whereHas('student', function ($q) use ($year) {
                $q->where('year', $year);
            });
        }
    
        // Fetch and filter to unique entries by student and course
        $studentAttendances = $query->get()->unique(function ($attendance) {
            return $attendance->student_id . '-' . $attendance->course_id;
        })->sortBy('entered_at');
    
        $pdf = Pdf::loadView('faculty.student_logbook', compact('studentAttendances'))
                  ->setPaper('a4', 'landscape');
    
        return $pdf->stream('student_logbook.pdf');
    }
    


    public function showAttendanceReport(Request $request)
    {
        $courseName = $request->input('course_name');
        $courseCode = $request->input('course_code'); 
        $instructorSchedule = $request->input('instructor_schedule');

        $date = now()->format('Y-m-d');

        $studentsInCourse = Student::where('course_name', $courseName)->get();

        $studentAttendances = Attendance::whereDate('entered_at', $date)
            ->whereHas('course', function ($query) use ($courseName, $courseCode) {
                $query->where('course_name', $courseName)
                    ->where('course_code', $courseCode);
            })->get();

        $presentStudentIds = $studentAttendances->pluck('student_id')->toArray();

        $absentStudents = $studentsInCourse->whereNotIn('id', $presentStudentIds);

        $totalPresent = $studentAttendances->count();
        $totalAbsent = $absentStudents->count();

        return view('attendance_report', [
            'studentAttendances' => $studentAttendances,
            'absentStudents' => $absentStudents,
            'date' => $date,
            'totalPresent' => $totalPresent,
            'totalAbsent' => $totalAbsent,
            'courseName' => $courseName,
            'courseCode' => $courseCode 
        ]);
    }

    

}