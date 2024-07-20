<!-- resources/views/admin/course/create.blade.php -->

@extends('layouts.admin')

@section('content')
<div class="form-container">
    <h2>Add New Course</h2>
    <form method="POST" action="{{ route('admin.course.store') }}">
        @csrf
        <div class="form-group">
            <label for="name">Course Name</label>
            <input type="text" class="form-control" id="course_name" name="course_name" required>
        </div>
        <div class="form-group">
            <label for="course_code">Course Code</label>
            <input type="text" class="form-control" id="course_code" name="course_code" required>
        </div>
        <div class="form-group">
            <label for="program">Program</label>
            <select class="form-control" id="program" name="program" required>
                @foreach($programs as $program)
                    <option value="{{ $program }}">{{ $program }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Add Course</button>
    </form>
</div>
@endsection
