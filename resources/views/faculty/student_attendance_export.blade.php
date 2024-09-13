<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Attendance</title>
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
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/finallogo.png') }}" alt="Logo">
        <h2>Student Attendance</h2>
    </div>
    <table class="table">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Course</th>
                <th>Date</th>
                <th>Time In</th>
                <th>Time Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach($studentAttendances as $attendance)
            <tr>
                <td>{{ $attendance->student->first_name }} {{ $attendance->student->last_name }}</td>
                <td>{{ $attendance->course->name }}</td>
                <td>{{ $attendance->entered_at->format('Y-m-d') }}</td>
                <td>{{ $attendance->entered_at->format('H:i:s') }}</td>
                <td>{{ $attendance->exited_at ? $attendance->exited_at->format('H:i:s') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
