@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Faculty Attendance</h2>
    <div class="action-bar d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('attendance.export') }}" class="btn btn-primary">Export to Excel</a>
    </div>
    <table class="table table-bordered mt-3">
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
