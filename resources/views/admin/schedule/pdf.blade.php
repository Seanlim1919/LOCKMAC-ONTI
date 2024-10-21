<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class Plotting</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px; 
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 0.7in;
            margin-bottom: 10px;
        }

        .header img {
            height: 100%;
            margin-left: 5px; 
            margin-right: 5px;
        }

        .header h2 {
            margin: 0;
            font-size: 18px; 
            flex-grow: 1;
            text-align: center;
        }

        .info-box {
            text-align: left; 
            margin-bottom: 10px; 
            margin-left: 5px; 
        }

        .table-container {
            width: 100%;
            margin: 0 auto;
        }

        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px; 
        }

        .schedule-table th, .schedule-table td {
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
            padding: 2px; 
            font-size: 12px; 
        }

        .schedule-table th:first-child,
        .schedule-table td:first-child {
            width: 12%; 
        }

        .schedule-table th:not(:first-child),
        .schedule-table td:not(:first-child) {
            width: calc((100% - 12%) / 7); 
        }

        .schedule-table th {
            background-color: #000;
            color: #fff;
        }

        .time-slot {
            height: 38px; 
        }

        .time-block {
            background-color: #d3d3d3;
            color: #000;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 15px; 
            text-align: center;
            font-size: 10px; 
            padding: 5px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/finallogo.png') }}" alt="Logo">
        <h2>Mac Laboratory Schedule</h2>
    </div>

    <div class="info-box">
        <strong>{{ $semester }} Semester</strong> <br>
        <strong>School Year:</strong> {{ $year_in }} - {{ $year_out }}
    </div>

    <div class="table-container">
        <table class="schedule-table">
            <thead>
                <tr>
                    <th>TIME</th>
                    <th>MONDAY</th>
                    <th>TUESDAY</th>
                    <th>WEDNESDAY</th>
                    <th>THURSDAY</th>
                    <th>FRIDAY</th>
                    <th>SATURDAY</th>
                    <th>SUNDAY</th>
                </tr>
            </thead>
            <tbody>
                @for ($hour = 7; $hour < 19; $hour++)
                    <tr>
                        <td class="time-slot">{{ formatTime($hour) }} - {{ formatTime($hour + 1) }}</td>
                        @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                            @php
                                $scheduleForHour = $schedules->first(function($schedule) use ($day, $hour) {
                                    $startHour = (int) substr($schedule->start_time, 0, 2);
                                    $endHour = (int) substr($schedule->end_time, 0, 2);
                                    return $schedule->day == $day && $hour >= $startHour && $hour < $endHour;
                                });

                                if ($scheduleForHour) {
                                    $startHour = (int) substr($scheduleForHour->start_time, 0, 2);
                                    $endHour = (int) substr($scheduleForHour->end_time, 0, 2);
                                    $rowspan = $endHour - $startHour;
                                    $color = '#ffc107';
                                }
                            @endphp

                            @if ($scheduleForHour && $hour == $startHour)
                                <td class="time-slot time-block" style="background-color: {{ $color }}" rowspan="{{ $rowspan }}">
                                    <div>
                                        <strong>{{ $scheduleForHour->course_code }}</strong><br>
                                        @if($scheduleForHour->faculty->gender == 'male')
                                            Mr.
                                        @else
                                            Ms.
                                        @endif
                                        {{ ucwords(strtolower($scheduleForHour->faculty->last_name)) }}<br>
                                        {{ $scheduleForHour->program }} - {{ $scheduleForHour->year }}{{ $scheduleForHour->section }}<br>
                                        {{ $scheduleForHour->location }}
                                    </div>
                                </td>
                            @elseif (!$schedules->first(function($schedule) use ($day, $hour) {
                                $startHour = (int) substr($schedule->start_time, 0, 2);
                                $endHour = (int) substr($schedule->end_time, 0, 2);
                                return $schedule->day == $day && $hour >= $startHour && $hour < $endHour;
                            }))
                                <td class="time-slot"></td>
                            @endif
                        @endforeach
                    </tr>
                @endfor
            </tbody>
        </table>
    </div>

    <footer>    
        Generated by LockMac
    </footer>
</body>
</html>

@php
// If you have a global function to format time, include it or make sure it is available globally
// function formatTime($hour) {
//     $period = $hour < 12 ? 'AM' : 'PM';
//     $formattedHour = $hour % 12;
//     $formattedHour = $formattedHour == 0 ? 12 : $formattedHour;
//     return sprintf('%d:00 %s', $formattedHour, $period);
// }
@endphp
