@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="page-title">SCHEDULE</h2>
    <div class="action-bar d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.schedule.create') }}" class="btn btn-primary">Add New Schedule</a>
        <a href="{{ route('admin.schedule.export') }}" class="btn btn-secondary">Export</a>
    </div>
    <div class="mb-3">
        <form method="GET" action="{{ route('admin.schedule.index') }}" class="d-flex align-items-center">
            <select name="faculty_id" class="form-control mr-2">
                <option value="">- Please select here -</option>
                @foreach ($faculties as $faculty)
                    <option value="{{ $faculty->id }}" {{ $faculty_id == $faculty->id ? 'selected' : '' }}>
                        {{ $faculty->first_name }} {{ $faculty->last_name }}
                    </option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-primary2">View Schedule</button>
        </form>
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
                                <td class="time-slot" rowspan="{{ $rowspan }}">
                                    <div class="highlight">
                                        <div>
                                            {{ $scheduleForHour->course_code }}<br>
                                            {{ $scheduleForHour->faculty->last_name }}<br>
                                            {{ $scheduleForHour->program }} - {{ $scheduleForHour->year }}{{ $scheduleForHour->section }}
                                        </div>
                                        <div class="actions">
                                            <form action="{{ route('admin.schedule.destroy', $scheduleForHour->id) }}" method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-icon delete">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
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
