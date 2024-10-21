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
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f2f2f2;
        }
        .summary {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .summary h3 {
            margin: 0 0 10px;
            font-size: 18px;
            color: #333;
        }
        .absent-list {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/finallogo.png') }}" alt="Logo">
        <h2>Student Attendance for {{ $course->course_name }}</h2>
    </div>

    @foreach($summaries as $date => $summary)
    <h3>Attendance Records for {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Date</th>
                <th>Time In</th>
                <th>Time Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach($summary['attendances'] as $attendance)
                <tr>
                    <td>{{ $attendance->student->first_name }} {{ $attendance->student->last_name }}</td>
                    <td>{{ $attendance->entered_at->format('m-d-Y') }}</td>
                    <td>{{ $attendance->entered_at->format('g:i A') }}</td>
                    <td>{{ $attendance->exited_at ? $attendance->exited_at->format('g:i A') : 'N/A' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <h3>Summary for {{ \Carbon\Carbon::parse($date)->format('F j, Y') }}</h3>
        <table class="table">
            <tr>
                <th>Total Present</th>
                <th>Total Absent</th>
            </tr>
            <tr>
                <td>{{ $summary['totalPresent'] }}</td>
                <td>{{ $summary['totalAbsent'] }}</td>
            </tr>
        </table>

        <div class="absent-list">   
            @if($summary['totalAbsent'] > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name of Absent Students</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summary['absentStudents'] as $student)
                            <tr>
                                <td>{{ ucwords(strtolower($student->first_name)) }} {{ ucwords(strtolower($student->last_name)) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>No students were absent.</p>
            @endif
        </div>
    </div>
@endforeach

</body>
</html>
