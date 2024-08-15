<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FacultyAttendanceExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Attendance::with('faculty')->get()->map(function ($attendance) {
            return [
                'Faculty Name' => $attendance->faculty->first_name . ' ' . $attendance->faculty->last_name,
                'Entered At' => $attendance->entered_at,
                'Exited At' => $attendance->exited_at,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Faculty Name',
            'Entered At',
            'Exited At',
        ];
    }
}
