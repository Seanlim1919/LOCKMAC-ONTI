<body>
    <html>
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Student Attendance Calendar</h1>

    <div id='calendar'></div>

    <!-- Display Attendance Logs -->
    <div id="attendance-logs" style="display:none;">
        <h2>Attendance Logs for <span id="selected-date"></span></h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Student Number</th>
                    <th>Student Name</th>
                    <th>Program, Year & Section</th>
                    <th>Course</th>
                    <th>Entered At</th>
                    <th>Exited At</th>
                </tr>
            </thead>
            <tbody id="logs-body">
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/5.11.0/main.min.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');

        var calendar = new FullCalendar.Calendar(calendarEl, {
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            initialView: 'dayGridMonth',
            events: '/api/attendance-events', // URL to fetch events
            dateClick: function(info) {
                fetchAttendanceLogs(info.dateStr);
            }
        });

        calendar.render();

        function fetchAttendanceLogs(date) {
            fetch(`/api/attendance-logs?date=${date}`)
                .then(response => response.json())
                .then(data => {
                    displayAttendanceLogs(data, date);
                })
                .catch(error => console.error('Error fetching attendance logs:', error));
        }

        function displayAttendanceLogs(data, date) {
            document.getElementById('selected-date').textContent = date;
            const logsBody = document.getElementById('logs-body');
            logsBody.innerHTML = '';

            data.forEach(attendance => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${attendance.student_number}</td>
                    <td>${attendance.student_name}</td>
                    <td>${attendance.program} - ${attendance.year}${attendance.section}</td>
                    <td>${attendance.course}</td>
                    <td>${attendance.entered_at}</td>
                    <td>${attendance.exited_at}</td>
                `;
                logsBody.appendChild(row);
            });

            document.getElementById('attendance-logs').style.display = 'block';
        }
    });
</script>
</body>
</html>
