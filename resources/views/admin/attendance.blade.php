@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Faculty Attendance</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Faculty Name</th>
                <th>Entered At</th>
                <th>Exited At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($facultyAttendances as $attendance)
            <tr>
                <td>{{ $attendance->faculty->first_name }} {{ $attendance->faculty->last_name }}</td>
                <td>{{ $attendance->entered_at }}</td>
                <td>{{ $attendance->exited_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
