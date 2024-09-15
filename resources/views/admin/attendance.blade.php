@extends('layouts.admin')

@section('content')

<div class="container">

    <h2>Faculty Logs</h2>

    <div class="action-bar">
        <a href="{{ route('attendance.export.pdf') }}" class="btn btn-quaternary">Export to PDF</a>
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
                <td>{{ $attendance->user->first_name }} {{ $attendance->user->last_name }}</td>
                <td>{{ $attendance->entered_at }}</td>
                <td>{{ $attendance->exited_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination justify-content-center">
        {{ $facultyAttendances->appends(request()->input())->links('vendor.pagination.custom-pagination') }}
    </div>

</div>

@endsection
