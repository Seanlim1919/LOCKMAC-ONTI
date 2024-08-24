<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use App\Models\Attendance; // Import the Attendance model
use Carbon\Carbon; // Ensure Carbon is imported for handling dates

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
        return Attendance::with('user')->get()->map(function ($attendance) {
            // Handle null values and ensure proper formatting
            $enteredAt = $attendance->entered_at ? Carbon::parse($attendance->entered_at) : null;
            $exitedAt = $attendance->exited_at ? Carbon::parse($attendance->exited_at) : null;

            return [
                'Faculty Name' => $attendance->user ? ($attendance->user->first_name . ' ' . $attendance->user->last_name) : 'N/A',
                'Date' => $enteredAt ? $enteredAt->format('Y-m-d') : 'N/A',
                'Time In' => $enteredAt ? $enteredAt->format('H:i:s') : 'N/A',
                'Time Out' => $exitedAt ? $exitedAt->format('H:i:s') : 'N/A',
                'Status' => $attendance->status ?? 'N/A',
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
