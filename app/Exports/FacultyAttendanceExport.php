<?php

namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class FacultyAttendanceExport implements FromCollection, WithHeadings
{
    /**
     * Return a collection of data to be exported.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Fetch the attendance data with related faculty
        return Attendance::with('faculty')->get()->map(function ($attendance) {
            return [
                'Faculty Name' => $attendance->faculty->name ?? 'N/A',
                'Date' => $attendance->date->format('Y-m-d'),
                'Time In' => $attendance->time_in->format('H:i:s'),
                'Time Out' => $attendance->time_out ? $attendance->time_out->format('H:i:s') : 'N/A',
                'Status' => $attendance->status,
            ];
        });
    }

    /**
     * Define the headings for the export file.
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Faculty Name',
            'Date',
            'Time In',
            'Time Out',
            'Status',
        ];
    }
}
