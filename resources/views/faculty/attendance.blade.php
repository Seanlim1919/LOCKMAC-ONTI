@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Student Logs</h1>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div style="display: inline-flex; align-items: center; flex-grow: 1;">
            <form method="GET" action="{{ route('attendance.students') }}" id="search-form" style="display: inline-flex; align-items: center; width: 100%;">
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" id="search-input" placeholder="Search" style="width: 250px; margin-right: 5px;">
                <button type="button" class="btn btn-secondary ml-1" data-toggle="modal" data-target="#filterModal">
                <i class="fas fa-filter"></i>
            </button>
            </form>
        </div>

        <div style="display: inline-flex; gap: 10px;">
        <form method="POST" action="{{ route('attendance.students.export') }}">
            @csrf
            <input type="hidden" name="start_date" value="{{ request('start_date') }}">
            <input type="hidden" name="end_date" value="{{ request('end_date') }}">
            <input type="hidden" name="section" value="{{ request('section') }}">
            <input type="hidden" name="course" value="{{ request('course') }}">
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#exportAttendanceModal">Export Attendance</button>
        </form>


            <form method="POST" action="{{ route('attendance.students.exportPdf') }}">
                @csrf
                <input type="hidden" name="date" value="{{ request('date') }}">
                <input type="hidden" name="section" value="{{ request('section') }}">
                <input type="hidden" name="course" value="{{ request('course') }}">
                <input type="hidden" name="program" value="{{ request('program') }}">
                <input type="hidden" name="year" value="{{ request('year') }}">
                <button type="submit" class="btn btn-primary">Export Logbook</button>
            </form>
        </div>
    </div>

<!-- Export Attendance Modal -->
<div class="modal fade" id="exportAttendanceModal" tabindex="-1" role="dialog" aria-labelledby="exportAttendanceModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document" style="max-width: 800px; margin: auto;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportAttendanceModalLabel">Export Attendance</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-left: auto; color: black; font-size: 20px;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="exportAttendanceForm" action="{{ route('attendance.students.export') }}" method="POST">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="start_date">Start Date</label>
                                <input type="date" class="form-control" id="start_date" name="start_date" value="{{ request('start_date') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="end_date">End Date</label>
                                <input type="date" class="form-control" id="end_date" name="end_date" value="{{ request('end_date') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="program">Program</label>
                                <select class="form-control" id="program" name="program" required>
                                    <option value="">Select Program</option>
                                    @foreach($allSchedules as $program)  
                                        <option value="{{ $program }}">{{ $program }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="year">Year</label>
                                <select class="form-control" id="year" name="year" required>
                                    <option value="">Select Year</option>
                                    @foreach($years as $year) 
                                        <option value="{{ $year }}">{{ $year }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="section">Section</label>
                                <select class="form-control" id="section" name="section" required>
                                    <option value="">Select Section</option>
                                    @foreach($sections as $sectionOption) 
                                        <option value="{{ $sectionOption }}">{{ $sectionOption }}</option>
                                    @endforeach 
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="course">Course</label>
                                <select class="form-control" id="course" name="course" required>
                                    <option value="">Select Course</option>
                                    @foreach($courses as $courseOption)
                                        <option value="{{ $courseOption->id }}">{{ $courseOption->course_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Export</button>
                </form>
            </div>
        </div>
    </div>
</div>


    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" role="dialog" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document" style="max-width: 800px; margin: auto; margin-top:10%">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Options</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="margin-left: auto; color: black; font-size: 20px;">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="GET" action="{{ route('attendance.students') }}" id="filter-form">
                        <input type="hidden" name="search" value="{{ $search ?? '' }}">
                        <div class="form-group">
                            <label for="filter-date">Date</label>
                            <input type="date" name="date" id="filter-date" class="form-control" value="{{ request('date') }}">
                        </div>
                        <div class="form-group">
                            <label for="filter-course">Course</label>
                            <select name="course" id="filter-course" class="form-control">
                                <option value="">All Courses</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>{{ $course->course_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter-program">Program</label>
                            <select name="program" id="filter-program" class="form-control">
                                <option value="">All</option>
                                <option value="BSIT" {{ request('program') == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                                <option value="BLIS" {{ request('program') == 'BLIS' ? 'selected' : '' }}>BLIS</option>
                                <option value="BSCS" {{ request('program') == 'BSCS' ? 'selected' : '' }}>BSCS</option>
                                <option value="BSIS" {{ request('program') == 'BSIS' ? 'selected' : '' }}>BSIS</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter-year">Year</label>
                            <select name="year" id="filter-year" class="form-control">
                                <option value="">All</option>
                                <option value="1" {{ request('year') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ request('year') == '2' ? 'selected' : '' }}>2</option>
                                <option value="3" {{ request('year') == '3' ? 'selected' : '' }}>3</option>
                                <option value="4" {{ request('year') == '4' ? 'selected' : '' }}>4</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter-section">Section</label>
                            <select name="section" id="filter-section" class="form-control">
                                <option value="">All</option>
                                @foreach ($sections as $section)
                                    <option value="{{ $section }}" {{ request('section') == $section ? 'selected' : '' }}>{{ $section }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Student Number</th>
                <th>Student Name</th>
                <th>Student Info</th>
                <th>Course Name</th>
                <th>Entered At</th>
                <th>Exited At</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($studentAttendances as $attendance)
            <tr>
                <td>{{ $attendance->student->student_number ?? 'N/A' }}</td>
                <td>{{ $attendance->student->first_name }} {{ $attendance->student->last_name }}</td>
                <td>{{ $attendance->student->program ?? 'N/A' }} - {{ $attendance->student->year ?? 'N/A' }} {{ $attendance->student->section ?? 'N/A' }}</td>
                <td>{{ $attendance->course->course_name ?? 'N/A' }}</td>
                <td>{{ $attendance->entered_at ? $attendance->entered_at->format('m-d-Y h:i A') : 'N/A' }}</td>
                <td>{{ $attendance->exited_at ? $attendance->exited_at->format('m-d-Y h:i A') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection


@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterButton = document.getElementById('filter-button');
        const filterOptions = document.getElementById('filter-options');
        const searchInput = document.getElementById('search-input');

        filterButton.addEventListener('click', function () {
            const rect = filterButton.getBoundingClientRect();
            filterOptions.style.left = `${rect.left}px`;
            filterOptions.style.top = `${rect.bottom}px`;
            filterOptions.style.display = filterOptions.style.display === 'none' || filterOptions.style.display === '' ? 'block' : 'none';
        });

        document.addEventListener('click', function (event) {
            if (!filterButton.contains(event.target) && !filterOptions.contains(event.target)) {
                filterOptions.style.display = 'none';
            }
        });
    });
</script>
@endsection
