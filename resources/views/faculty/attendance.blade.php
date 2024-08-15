@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Student Attendance</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Student Number</th>
                <th>Entered At</th>
                <th>Exited At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($studentAttendances as $attendance)
            <tr>
                <td>{{ $attendance->student->first_name ?? 'Unknown' }} {{ $attendance->student->last_name ?? '' }}</td>
                <td>{{ $attendance->student->student_number }}</td>
                <td>{{ $attendance->entered_at }}</td>
                <td>{{ $attendance->exited_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
