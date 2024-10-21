<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Logbook</title>
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
            justify-content: space-between;
            height: 0.7in;
            margin-bottom: 20px;
        }

        .header img {
            height: 100%;
            margin-left: 10px;
            margin-right: 10px;
        }

        .header h2 {
            margin: 0;
            font-size: 20px;
            flex-grow: 1;
            text-align: center;
            margin-bottom: 30px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
        }
        .footer {
            margin-top: 20px;
            text-align: center;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/logbooklogo.png') }}" alt="Logo">
        <h2>Mac Laboratory Logbook</h2>
    </div>
    <table class="table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Student Name</th>
            <th>PC Number</th>
            <th>Student Number</th>
            <th>Program, Year & Section</th>
            <th>Instructor</th>
            <th>Time</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($studentAttendances as $attendance)
        <tr>
            @if ($attendance->entered_at instanceof \Carbon\Carbon)
                <td>{{ $attendance->entered_at->format('Y-m-d') }}</td>
            @endif
            <td>{{ ucwords(strtolower($attendance->student->first_name)) }} {{ ucwords(strtolower($attendance->student->last_name)) }}</td>
            <td>{{ $attendance->student->pc_number }}</td>
            <td>{{ $attendance->student->student_number }}</td>
            <td>{{ $attendance->student->program }} - {{ $attendance->student->year }} {{ $attendance->student->section }}</td>
            <td>{{ getFacultyTitle($attendance->faculty) }} {{ ucwords(strtolower($attendance->faculty->first_name)) }} {{ ucwords(strtolower($attendance->faculty->last_name ?? 'N/A')) }}</td>
            <td>{{ $attendance->entered_at->format('g:i A') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

    <div class="footer">
        SYSTEM GENERATED: SIGNATURE IS NOT NEEDED
    </div>
</body>
</html>
