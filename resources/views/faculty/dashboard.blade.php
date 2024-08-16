<!-- resources/views/faculty/dashboard.blade.php -->

@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="page-title">DASHBOARD</h1>
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
                                <td class="time-slot" rowspan="{{ $rowspan }}">
                                    <div class="highlight">
                                        <div>
                                            {{ $scheduleForHour->course_code }}<br>
                                            {{ getFacultyTitle($scheduleForHour->faculty) }} {{ $scheduleForHour->faculty->last_name }}<br>
                                            {{ $scheduleForHour->program }} - {{ $scheduleForHour->year }}{{ $scheduleForHour->section }}
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
