@extends('layouts.admin')

@section('content')
<div class="form-container">
    <h2>Add New Course</h2>
    <form method="POST" action="{{ route('admin.course.store') }}">
        @csrf
        <div class="form-group">
            <label for="course_name">Course Name</label>
            <input type="text" class="form-control" id="course_name" name="course_name" required>
        </div>
        <div class="form-group">
            <label for="course_code">Course Code</label>
            <input type="text" class="form-control" id="course_code" name="course_code" required>
        </div>
        <div class="form-group">
            <label for="program">Program</label>
            <select class="form-control" style="width: 95%;" id="program" name="program" required>
                @foreach($programs as $program)
                    <option value="{{ $program }}">{{ $program }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="sem_avail">Semester</label>
            <select class="form-control" id="sem_avail" name="sem_avail" required>
                <option value="First">First Semester</option>
                <option value="Second">Second Semester</option>
            </select>
        </div>
        <div class="form-group">
            <label for="year_avail">Year Level</label>
            <select class="form-control" id="year_avail" name="year_avail" required>
                <option value="1">1st Year</option>
                <option value="2">2nd Year</option>
                <option value="3">3rd Year</option>
                <option value="4">4th Year</option>
            </select>
        </div>
        <button type="submit" class="course">Add Course</button>
    </form>
</div>
@endsection
