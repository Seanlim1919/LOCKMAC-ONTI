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
        <label for="year">Year</label>
        <select name="year" id="year" class="form-control" required>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
            <option value="4">4</option>
        </select>
        </div>
        <div class="form-group">
            <label for="section">Section</label>
            <select name="section" id="section" class="form-control" required>
                <option value="A">A</option>
                <option value="B">B</option>
                <option value="C">C</option>
                <option value="D">D</option>
                <option value="E">E</option>
                <option value="F">F</option>
                <option value="G">G</option>
                <option value="H">H</option>
            </select>
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
