<?php

namespace App\Exports;

use App\Models\Schedule;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SchedulesExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Schedule::with('course', 'faculty')->get()->map(function ($schedule) {
            return [
                'Course Code' => $schedule->course->course_code,
                'Course Name' => $schedule->course->course_name,
                'Faculty' => $schedule->faculty->first_name . ' ' . $schedule->faculty->last_name,
                'Day' => $schedule->day,
                'Start Time' => $schedule->start_time,
                'End Time' => $schedule->end_time,
            ];
        });
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'Course Code',
            'Course Name',
            'Faculty',
            'Day',
            'Start Time',
            'End Time',
        ];
    }
}
