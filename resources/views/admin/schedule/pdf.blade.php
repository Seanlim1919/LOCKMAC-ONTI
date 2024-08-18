<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Class Plotting</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
        }
        .header {
            display: flex;
            align-items: center;
            justify-content: space-between; /* Adjusts spacing between images and h2 */
            height: 0.7in; /* Header height set to half an inch */
            margin-bottom: 20px;
        }

        .header img {
            height: 100%; /* Ensure images fill the header height */
            margin-left: 10px; /* Adjust as needed for spacing */
            margin-right: 10px; /* Adjust as needed for spacing */
        }

        .header h2 {
            margin: 0;
            font-size: 20px;
            flex-grow: 1; /* Allows the h2 to take up remaining space */
            text-align: center; /* Centers the text within its allocated space */
            margin-bottom: 30px;
        }

        .table-container {
            width: 100%;
            margin: 0 auto;
            padding-bottom: 5mm; 
        }
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .schedule-table th, .schedule-table td {
            border: 1px solid #000;
            text-align: center;
            vertical-align: middle;
            padding: 6px; /* Reduce padding slightly */
            font-size: 17px;
        }
        .schedule-table th:first-child,
        .schedule-table td:first-child {
            width: 13%; /* Set the width of the Time column */
        }
        .schedule-table th:not(:first-child),
        .schedule-table td:not(:first-child) {
            width: calc((100% - 13%) / 7); /* Evenly distribute remaining space among the days of the week */
        }
        .schedule-table th {
            background-color: #000;
            color: #fff;
        }
        .time-slot {
            height: 30px; /* Adjust the height to match the layout */
            font-size
        }
        .highlight {
            background-color: #f0f0f0;
        }
        .time-block {
            background-color: #d3d3d3;
            color: #000;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/finallogo.png') }}" alt="Logo">
        <h2>Mac Laboratory Schedule</h2>
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
                                    $color = '#ffc107'; // Define your color logic here if needed
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
                                        {{ $scheduleForHour->faculty->last_name }}<br>
                                        {{ $scheduleForHour->program }} - {{ $scheduleForHour->year }} {{ $scheduleForHour->section }}<br>
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
</body>
</html>

@php
function formatTime($hour) {
    $period = $hour < 12 ? 'AM' : 'PM';
    $formattedHour = $hour % 12;
    $formattedHour = $formattedHour == 0 ? 12 : $formattedHour;
    return sprintf('%d:00 %s', $formattedHour, $period);
}
@endphp
