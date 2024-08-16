<?php
namespace App\Exports;

use App\Models\StudentAttendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class StudentAttendanceExport implements FromCollection, WithHeadings
{
    protected $date;
    protected $course;
    protected $program;
    protected $year;
    protected $section;

    public function __construct($date, $course, $program, $year, $section)
    {
        $this->date = $date;
        $this->course = $course;
        $this->program = $program;
        $this->year = $year;
        $this->section = $section;
    }

    public function collection()
    {
        $query = StudentAttendance::query()->with(['student', 'course']);

        if ($this->date) {
            $query->whereDate('entered_at', $this->date);
        }

        if ($this->course) {
            $query->where('course_id', $this->course);
        }

        if ($this->program) {
            $query->whereHas('student', function ($q) {
                $q->where('program', $this->program);
            });
        }

        if ($this->year) {
            $query->whereHas('student', function ($q) {
                $q->where('year', $this->year);
            });
        }

        if ($this->section) {
            $query->whereHas('student', function ($q) {
                $q->where('section', $this->section);
            });
        }

        return $query->get()->map(function ($attendance) {
            return [
                $attendance->student ? $attendance->student->first_name . ' ' . $attendance->student->last_name : 'N/A',
                $attendance->course ? $attendance->course->course_name : 'N/A',
                $attendance->entered_at->format('Y-m-d'),
                $attendance->status,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Student Name',
            'Course Name',
            'Date',
            'Status'
        ];
    }
}

