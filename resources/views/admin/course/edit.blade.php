<!-- resources/views/admin/course/edit.blade.php -->

@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Edit Course</h2>
    <form method="POST" action="{{ route('admin.course.update', $course->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Course Name</label>
            <input type="text" class="form-control" id="name" name="name" value="{{ $course->course_name }}" required>
        </div>
        <div class="form-group">
            <label for="course_code">Course Code</label>
            <input type="text" class="form-control" id="course_code" name="course_code" value="{{ $course->course_code }}" required>
        </div>
        <div class="form-group">
            <label for="program">Program</label>
            <select class="form-control" id="program" name="program" required>
                @foreach($programs as $program)
                    <option value="{{ $program }}" {{ $course->program == $program ? 'selected' : '' }}>{{ $program }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Course</button>
    </form>
</div>
@endsection
