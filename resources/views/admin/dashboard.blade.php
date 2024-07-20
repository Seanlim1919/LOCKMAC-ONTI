@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="page-title">Dashboard</h2>
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="info-box bg-primary text-white">
                <div class="info-box-content">
                    <span class="info-box-text">Number of Faculty</span>
                    <span class="info-box-number">{{ $facultyCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-success text-white">
                <div class="info-box-content">
                    <span class="info-box-text">Number of Students</span>
                    <span class="info-box-number">{{ $studentCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-warning text-white">
                <div class="info-box-content">
                    <span class="info-box-text">Number of Courses</span>
                    <span class="info-box-number">{{ $courseCount }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="info-box bg-danger text-white">
                <div class="info-box-content">
                    <span class="info-box-text">Attendance Percentage</span>
                    <span class="info-box-number">{{ number_format($attendancePercentage, 2) }}%</span>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Time</th>
                <th>M</th>
                <th>T</th>
                <th>W</th>
                <th>Th</th>
                <th>F</th>
                <th>Sat</th>
                <th>S</th>
            </tr>
        </thead>
        <tbody>
            @for ($hour = 7; $hour < 18; $hour++)
                <tr>
                    <td>{{ formatTime($hour) }} - {{ formatTime($hour + 1) }}</td>
                    @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                        @php
                            $scheduleForHour = $schedules->first(function($schedule) use ($day, $hour) {
                                $startHour = (int) substr($schedule->start_time, 0, 2);
                                $endHour = (int) substr($schedule->end_time, 0, 2);
                                return $schedule->day == $day && $hour >= $startHour && $hour < $endHour;
                            });
                        @endphp
                        @if ($scheduleForHour)
                            @php
                                $startHour = (int) substr($scheduleForHour->start_time, 0, 2);
                                $endHour = (int) substr($scheduleForHour->end_time, 0, 2);
                                $rowspan = $endHour - $startHour;
                            @endphp
                            @if ($hour == $startHour)
                                <td class="time-slot" rowspan="{{ $rowspan }}" onclick="window.location='{{ route('admin.schedule.edit', $scheduleForHour->id) }}'" data-toggle="tooltip" data-placement="top" title="{{ $scheduleForHour->course_name }} with {{ $scheduleForHour->faculty->first_name }} {{ $scheduleForHour->faculty->last_name }}">
                                    <div class="highlight">
                                        <div>
                                            {{ $scheduleForHour->course_code }}<br>
                                            {{ $scheduleForHour->faculty->last_name }}
                                        </div>
                                    </div>
                                </td>
                            @endif
                        @else
                            @if (!$schedules->first(function($schedule) use ($day, $hour) {
                                $startHour = (int) substr($schedule->start_time, 0, 2);
                                $endHour = (int) substr($schedule->end_time, 0, 2);
                                return $schedule->day == $day && $hour >= $startHour && $hour < $endHour;
                            }))
                                <td class="time-slot"></td>
                            @endif
                        @endif
                    @endforeach
                </tr>
            @endfor
        </tbody>
    </table>
</div>
@endsection

@php
function formatTime($hour) {
    $period = $hour < 12 ? 'AM' : 'PM';
    $formattedHour = $hour % 12;
    $formattedHour = $formattedHour == 0 ? 12 : $formattedHour;
    return sprintf('%d:00 %s', $formattedHour, $period);
}
@endphp
