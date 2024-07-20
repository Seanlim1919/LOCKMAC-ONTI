@extends('layouts.admin')

@section('content')
<div class="form-container">
    <h2>Add New Schedule</h2>
    <form action="{{ route('admin.schedule.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="faculty_id">Faculty</label>
            <select name="faculty_id" id="faculty_id" class="form-control">
                @foreach ($faculties as $faculty)
                    <option value="{{ $faculty->id }}">{{ $faculty->first_name }} {{ $faculty->last_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="course_id">Course</label>
            <select name="course_id" id="course_id" class="form-control">
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}">{{ $course->course_code }} - {{ $course->course_name }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="program">Program</label>
            <select class="form-control" id="program" name="program" required>
                <option value="BSIT">BSIT</option>
                <option value="BLIS">BLIS</option>
                <option value="BSCS">BSCS</option>
                <option value="BSIS">BSIS</option>
            </select>
        </div>
        <div class="form-group">
            <label for="year_and_section">Year & Section</label>
            <input type="text" class="form-control" id="year_and_section" name="year_and_section" required>
        </div>
        <div class="form-group">
            <label for="day">Day</label>
            <select name="day" id="day" class="form-control">
                <option value="Monday">Monday</option>
                <option value="Tuesday">Tuesday</option>
                <option value="Wednesday">Wednesday</option>
                <option value="Thursday">Thursday</option>
                <option value="Friday">Friday</option>
                <option value="Saturday">Saturday</option>
                <option value="Sunday">Sunday</option>
            </select>
        </div>
        <div class="form-group">
            <label for="start_time">Start Time</label>
            <input type="time" name="start_time" id="start_time" class="form-control" required>
        </div>
        <div class="form-group">
            <label for="end_time">End Time</label>
            <input type="time" name="end_time" id="end_time" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Schedule</button>
    </form>
</div>
@endsection
