@extends('layouts.app')

@section('content')
<div class="container">
    <h1>STUDENT ATTENDANCE</h1>

    <div class="btn-container">
        <form method="GET" action="{{ route('attendance.students') }}" id="search-form" class="d-flex align-items-center">
            <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" id="search-input" placeholder="Search">
            <button type="button" class="btn btn-secondary ml-1" id="filter-button">
                <i class="fas fa-filter"></i>
            </button>
        </form>
        <form method="POST" action="{{ route('attendance.students.export') }}">
            @csrf
            <input type="hidden" name="date" value="{{ request('date') }}">
            <input type="hidden" name="section" value="{{ request('section') }}">
            <input type="hidden" name="course" value="{{ request('course') }}">
            <button type="button" class="btn btn-primary">Export Attendance</button>
        </form>
        <form method="POST" action="{{ route('attendance.students.exportPdf') }}">
            @csrf
            <input type="hidden" name="date" value="{{ request('date') }}">
            <input type="hidden" name="section" value="{{ request('section') }}">
            <input type="hidden" name="course" value="{{ request('course') }}">
            <input type="hidden" name="program" value="{{ request('program') }}">
            <input type="hidden" name="year" value="{{ request('year') }}">
            <button type="button" class="btn btn-secondary">Export Logbook</button>
        </form>
    </div>

    <!-- Filter Options -->
    <div id="filter-options" class="dropdown-menu position-absolute" style="display: none;">
        <form method="GET" action="{{ route('attendance.students') }}" id="filter-form">
            <div class="form-group">
                <label for="filter-date">Date</label>
                <input type="date" name="date" id="filter-date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="form-group">
                <label for="filter-course">Course</label>
                <select name="course" id="filter-course" class="form-control">
                    <option value="">All Courses</option>
                    @foreach ($courses as $course)
                        @if($course)
                            <option value="{{ $course->id }}" {{ request('course') == $course->id ? 'selected' : '' }}>{{ $course->course_name }}</option>
                        @endif
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
                    <option value="A" {{ request('section') == 'A' ? 'selected' : '' }}>A</option>
                    <option value="B" {{ request('section') == 'B' ? 'selected' : '' }}>B</option>
                    <option value="C" {{ request('section') == 'C' ? 'selected' : '' }}>C</option>
                    <option value="D" {{ request('section') == 'D' ? 'selected' : '' }}>D</option>
                    <option value="E" {{ request('section') == 'E' ? 'selected' : '' }}>E</option>
                    <option value="F" {{ request('section') == 'F' ? 'selected' : '' }}>F</option>
                    <option value="G" {{ request('section') == 'G' ? 'selected' : '' }}>G</option>
                    <option value="H" {{ request('section') == 'H' ? 'selected' : '' }}>H</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Apply Filters</button>
        </form>
    </div>

<table class="table table-bordered">
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
            <td>{{ $attendance->course->name ?? 'N/A' }}</td>
            <td>{{ $attendance->entered_at ? $attendance->entered_at->format('m-d-Y h:i A') : 'N/A' }}</td>
            <td>{{ $attendance->exited_at ? $attendance->exited_at->format('m-d-Y h:i A') : 'N/A' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</div>
@endsection

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterButton = document.getElementById('filter-button');
        const filterOptions = document.getElementById('filter-options');
        const searchInput = document.getElementById('search-input');

        filterButton.addEventListener('click', function () {
            filterOptions.style.display = filterOptions.style.display === 'none' ? 'block' : 'none';
        });

        searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault(); 
                document.getElementById('search-form').submit();
            }
        });

        document.getElementById('filter-form').addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault(); 
                this.submit();
            }
        });
    });
</script>
