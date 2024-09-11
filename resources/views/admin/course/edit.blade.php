@extends('layouts.admin')

@section('content')
<div class="form-container">
    <h2>Edit Course</h2>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.course.update', $course->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="name">Course Name</label>
            <input type="text" class="form-control" id="course_name" name="course_name" value="{{ old('course_name', $course->course_name) }}" required>
        </div>
        <div class="form-group">
            <label for="course_code">Course Code</label>
            <input type="text" class="form-control" id="course_code" name="course_code" value="{{ old('course_code', $course->course_code) }}" required>
        </div>
        <div class="form-group">
            <label for="program">Program</label>
            <select class="form-control" id="program" name="program" required>
                @foreach($programs as $programOption)
                    <option value="{{ $programOption }}" {{ old('program', $course->program) == $programOption ? 'selected' : '' }}>{{ $programOption }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Course</button>
    </form>
</div>
@endsection
