<!DOCTYPE html>
<html>
<head>
    <title>Faculty Attendance Report</title>
    <style>
        @page {
            size: 8.5in 11in; /* Set the page size to letter (portrait) */
            margin: 0.5in 0.5in; /* Set the page margins */
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 1in; /* Adjust header height */
            margin-bottom: 0.5in;
        }

        .header img {
            height: 80%;
        }

        .header h2 {
            margin: 0;
            font-size: 18px; /* Adjust font size */
            text-align: center;
            flex-grow: 1;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1in; /* Add space below the table */
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="{{ public_path('images/portraitlogo.png') }}" alt="Logo">
        <h2>Faculty Attendance Report</h2>
    </div>
    <table>
        <thead>
            <tr>
                <th>Faculty Name</th>
                <th>Date</th>
                <th>Time In</th>
                <th>Time Out</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($facultyAttendances as $attendance)
                <tr>
                    <td>{{ $attendance['Faculty Name'] }}</td>
                    <td>{{ $attendance['Date'] }}</td>
                    <td>{{ $attendance['Time In'] }}</td>
                    <td>{{ $attendance['Time Out'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
