@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Faculty Logs</h2>
    @if (session('error'))
    <div class="alert alert-danger">
        {{ session('error') }}
    </div>
    @endif

    <div class="action-bar d-flex justify-content-between">
        <button id="toggleFilter" class="btn btn-dark">Filter Logs</button>
        <button class="btn btn-quaternary" data-toggle="modal" data-target="#exportModal">Export to PDF</button>
    </div>

    <form id="filterForm" action="{{ route('admin.attendance') }}" method="GET" class="mb-4 mt-3" style="display: none;">
        <div class="row">
            <div class="col-md-3">
                <label for="month">Month:</label>
                <select name="month" id="month" class="form-control">
                    <option value="">Select Month</option>
                    @foreach(range(1, 12) as $month)
                        <option value="{{ $month }}" {{ request('month') == $month ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::createFromDate(null, $month, 1)->format('F') }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="day">Day:</label>
                <select name="day" id="day" class="form-control">
                    <option value="">Select Day</option>
                    @foreach(range(1, 31) as $day)
                        <option value="{{ $day }}" {{ request('day') == $day ? 'selected' : '' }}>
                            {{ $day }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label for="year">Year:</label>
                <select name="year" id="year" class="form-control">
                    <option value="">Select Year</option>
                    @foreach(range(now()->year, now()->year - 5) as $year)
                        <option value="{{ $year }}" {{ request('year') == $year ? 'selected' : '' }}>
                            {{ $year }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label>&nbsp;</label>
                <button type="submit" class="btn btn-dark form-control">Apply Filter</button>
            </div>
        </div>
    </form>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Faculty Name</th>
                <th>Course Code</th>
                <th>Program, Year & Section</th>
                <th>Entered At</th>
                <th>Exited At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($facultyAttendances as $attendance)
            <tr>
                <td>
                    {{ ucwords(strtolower($attendance->user->first_name)) }} {{ ucwords(strtolower($attendance->user->last_name)) }}
                    @if ($attendance->user->role == 'admin') 
                        (Admin)
                    @endif
                </td>
                <td>
                    @if ($attendance->user->role == 'admin')
                        N/A
                    @else
                        {{ $attendance->schedule->course->course_code ?? 'N/A' }}
                    @endif
                </td>
                <td>
                    @if ($attendance->user->role == 'admin')
                        N/A
                    @else
                        {{ $attendance->schedule->program ?? 'N/A' }} - {{ $attendance->schedule->year ?? 'N/A' }}{{ $attendance->schedule->section ?? 'N/A' }}
                    @endif
                </td>
                <td>{{ \Carbon\Carbon::parse($attendance->entered_at)->format('m/d/Y g:i A') }}</td>
                <td>{{ $attendance->exited_at ? \Carbon\Carbon::parse($attendance->exited_at)->format('m/d/Y g:i A') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination justify-content-center">
        {{ $facultyAttendances->appends(request()->input())->links('vendor.pagination.custom-pagination') }}
    </div>

    <!-- Export Modal -->
    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Export Attendance to PDF</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="exportForm" action="{{ route('attendance.export.pdf') }}" method="GET">
                        <div class="form-group">
                            <label for="start_date">Start Date:</label>
                            <input type="date" class="form-control" name="start_date" required>
                        </div>
                        <div class="form-group">
                            <label for="end_date">End Date:</label>
                            <input type="date" class="form-control" name="end_date" required>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Export</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    document.getElementById('toggleFilter').addEventListener('click', function() {
        const filterForm = document.getElementById('filterForm');
        filterForm.style.display = filterForm.style.display === 'none' ? 'block' : 'none';
    });
</script>

@endsection
