@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="page-title">DASHBOARD</h2>

    <div class="row mb-4">
        <!-- Number of Faculty -->
        <div class="col-md-4">
            <div class="info-box">
                <div class="info-box-header">
                    <h4 class="info-box-title">Number of Faculty</h4>
                </div>
                <div class="info-box-content">
                    <p class="info-box-number">{{ $facultyCount }}</p>
                </div>
            </div>
        </div>
        
        <!-- Number of Students -->
        <div class="col-md-4">
            <div class="info-box">
                <div class="info-box-header">
                    <h4 class="info-box-title">Number of Students</h4>
                </div>
                <div class="info-box-content">
                    <p class="info-box-number">{{ $studentCount }}</p>
                </div>
            </div>
        </div>

        <!-- Number of Courses -->
        <div class="col-md-4">
            <div class="info-box">
                <div class="info-box-header">
                    <h4 class="info-box-title">Number of Courses</h4>
                </div>
                <div class="info-box-content">
                    <p class="info-box-number">{{ $courseCount }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Current Time -->
        <div class="col-md-4">
            <div class="info-box">
                <div class="info-box-header">
                    <h4 class="info-box-title">Current Time</h4>
                </div>
                <div class="info-box-content">
                    <i class="fas fa-clock info-box-icon"></i>
                    <p id="currentTime" class="info-box-number"></p>
                </div>
            </div>
        </div>

        <!-- Faculty Attendance -->
        <div class="col-md-4">
            <div class="info-box">
                <div class="info-box-header">
                    <h4 class="info-box-title">Faculty Logs Data Statistics</h4>
                </div>
                <div class="info-box-content">
                    <canvas id="facultyAttendanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Student Attendance -->
        <div class="col-md-4">
            <div class="info-box">
                <div class="info-box-header">
                    <h4 class="info-box-title">Student Attendance Data Statistics</h4>
                </div>
                <div class="info-box-content">
                    <canvas id="studentAttendanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Schedule Table Container -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="info-box">
                <div class="info-box-header">
                    <h4 class="info-box-title">Schedule</h4>
                </div>
                <div class="info-box-content">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Time</th>
                                    <th>Monday</th>
                                    <th>Tuesday</th>
                                    <th>Wednesday</th>
                                    <th>Thursday</th>
                                    <th>Friday</th>
                                    <th>Saturday</th>
                                    <th>Sunday</th>
                                </tr>
                            </thead>
                            <tbody>
                                @for ($hour = 7; $hour <= 18; $hour++)
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
                                            @if ($scheduleForHour && $hour == (int) substr($scheduleForHour->start_time, 0, 2))
                                                @php
                                                    $startHour = (int) substr($scheduleForHour->start_time, 0, 2);
                                                    $endHour = (int) substr($scheduleForHour->end_time, 0, 2);
                                                    $rowspan = $endHour - $startHour;
                                                @endphp
                                                <td class="time-slot occupied" rowspan="{{ $rowspan }}" >
                                                    <div>
                                                        <div>
                                                            {{ $scheduleForHour->course_code }}<br>
                                                            {{ strtoupper(getFacultyTitle($scheduleForHour->faculty)) }} {{ strtoupper($scheduleForHour->faculty->last_name) }}<br>
                                                            {{ $scheduleForHour->program }} - {{ $scheduleForHour->year }}{{ $scheduleForHour->section }}
                                                        </div>
                                                    </div>
                                                </td>
                                            @else
                                                <td class="time-slot"></td>
                                            @endif
                                        @endforeach
                                    </tr>
                                @endfor
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Function to update the current time every second
    function updateTime() {
        const now = new Date();
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        document.getElementById('currentTime').innerText = `${hours}:${minutes}:${seconds}`;
    }
    setInterval(updateTime, 1000);
    updateTime(); // Initial call

    // Faculty Attendance Chart
    const facultyAttendanceConfig = @json($facultyAttendanceData);
    new Chart(document.getElementById('facultyAttendanceChart').getContext('2d'), {
        type: 'bar',
        data: {
            labels: facultyAttendanceConfig.labels,
            datasets: [{
                label: 'Faculty Attendance',
                data: facultyAttendanceConfig.data,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Student Attendance Chart
    const studentAttendanceData = @json($studentAttendanceData);
    const studentAttendanceConfig = {
        type: 'bar',
        data: {
            labels: studentAttendanceData.labels,
            datasets: [{
                label: 'Student Attendance',
                data: studentAttendanceData.data,
                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                borderColor: 'rgba(153, 102, 255, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    };
    new Chart(document.getElementById('studentAttendanceChart').getContext('2d'), studentAttendanceConfig);
</script>
@endsection
