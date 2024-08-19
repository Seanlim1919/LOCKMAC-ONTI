<?php
namespace App\Exports;

use App\Models\StudentAttendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Maatwebsite\Excel\Events\AfterSheet;

class StudentAttendanceExport implements FromCollection, WithHeadings, WithStyles, ShouldAutoSize, WithEvents
{
    protected $date, $course, $program, $year, $section;

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
        return StudentAttendance::with(['student', 'course'])
            ->when($this->date, function ($q) {
                $q->whereDate('entered_at', $this->date);
            })
            ->when($this->course, function ($q) {
                $q->where('course_id', $this->course);
            })
            ->when($this->program, function ($q) {
                $q->whereHas('student', function ($q) {
                    $q->where('program', $this->program);
                });
            })
            ->when($this->year, function ($q) {
                $q->whereHas('student', function ($q) {
                    $q->where('year', $this->year);
                });
            })
            ->when($this->section, function ($q) {
                $q->whereHas('student', function ($q) {
                    $q->where('section', $this->section);
                });
            })
            ->get()
            ->map(function ($attendance) {
                return [
                    'student_number' => $attendance->student->student_number,
                    'student_name' => $attendance->student->first_name . ' ' . $attendance->student->last_name,
                    'program_year_section' => $attendance->student->program . ', ' . $attendance->student->year . '-' . $attendance->student->section,
                    'entered_at' => $attendance->entered_at,
                ];
            });
    }
    
    public function headings(): array
    {
        return [
            'Student Number',
            'Student Name',
            'Program, Year & Section',
            'Entered At'
        ];
    }
    

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold
            1 => ['font' => ['bold' => true]],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:H1')->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => [
                            'rgb' => '4CAF50', // Green background
                        ],
                    ],
                    'font' => [
                        'color' => [
                            'rgb' => 'FFFFFF', // White text
                        ],
                        'bold' => true,
                        'size' => 12,
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                ]);

                // Apply border and alignment styling to all cells
                $event->sheet->getStyle('A1:H' . $event->sheet->getHighestRow())->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                    ],
                ]);
            },
        ];
    }
}

