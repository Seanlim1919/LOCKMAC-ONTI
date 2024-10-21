@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="page-title">DASHBOARD</h2>

    <div class="row mb-4">
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

    <div class="row mb-4">
    <div class="col-md-12">
        <div class="info-box">
        <div class="info-box-header">
    <h4 class="info-box-title">Active Schedule</h4>
            @php
                $activeSchedule = $schedules->firstWhere('status', 1);
            @endphp

            @if ($activeSchedule && $activeSchedule->semester)
                <div class="d-inline">
                    <strong>Semester:</strong> {{ $activeSchedule->semester->semester_name }} 
                </div>
                <div class="d-inline ml-4">
                    <strong>School Year:</strong> {{ $activeSchedule->semester->start_year }}-{{ $activeSchedule->semester->end_year }}
                </div>
            @else
                <div class="d-inline">
                    No active schedule.
                </div>
            @endif
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
                                                return $schedule->day == $day && $schedule->status == 1 && $hour >= $startHour && $hour < $endHour;
                                            });
                                        @endphp

                                        @if ($scheduleForHour && $hour == (int) substr($scheduleForHour->start_time, 0, 2))
                                            @php
                                                $startHour = (int) substr($scheduleForHour->start_time, 0, 2);
                                                $endHour = (int) substr($scheduleForHour->end_time, 0, 2);
                                                $rowspan = $endHour - $startHour;
                                            @endphp
                                            <td class="time-slot occupied" rowspan="{{ $rowspan }}">
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
    function updateTime() {
        const now = new Date();
        let hours = now.getHours();
        const minutes = String(now.getMinutes()).padStart(2, '0');
        const seconds = String(now.getSeconds()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';

        hours = hours % 12;
        hours = hours ? hours : 12;

        const formattedHours = String(hours).padStart(2, '0');

        document.getElementById('currentTime').innerText = `${formattedHours}:${minutes}${ampm}`;
    }

    setInterval(updateTime, 1000);
    updateTime();


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