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
        
        <!-- Course Name Field -->
        <div class="form-group">
            <label for="course_name">Course Name</label>
            <input type="text" class="form-control" id="course_name" name="course_name" value="{{ old('course_name', $course->course_name) }}" required>
        </div>

        <!-- Course Code Field -->
        <div class="form-group">
            <label for="course_code">Course Code</label>
            <input type="text" class="form-control" id="course_code" name="course_code" value="{{ old('course_code', $course->course_code) }}" required>
        </div>

        <!-- Program Field -->
        <div class="form-group">
            <label for="program">Program</label>
            <select class="form-control" id="program" name="program" required>
                @foreach($programs as $programOption)
                    <option value="{{ $programOption }}" {{ old('program', $course->program) == $programOption ? 'selected' : '' }}>{{ $programOption }}</option>
                @endforeach
            </select>
        </div>

        <!-- Semester Availability Dropdown -->
        <div class="form-group">
            <label for="sem_avail">Semester</label>
            <select class="form-control" id="sem_avail" name="sem_avail" required>
                <option value="First" {{ old('sem_avail', $course->sem_avail) == 'First' ? 'selected' : '' }}>First Semester</option>
                <option value="Second" {{ old('sem_avail', $course->sem_avail) == 'Second' ? 'selected' : '' }}>Second Semester</option>
            </select>
        </div>

        <!-- Year Level Dropdown -->
        <div class="form-group">
            <label for="year_avail">Year Level</label>
            <select class="form-control" id="year_avail" name="year_avail" required>
                <option value="1" {{ old('year_avail', $course->year_avail) == '1' ? 'selected' : '' }}>1st Year</option>
                <option value="2" {{ old('year_avail', $course->year_avail) == '2' ? 'selected' : '' }}>2nd Year</option>
                <option value="3" {{ old('year_avail', $course->year_avail) == '3' ? 'selected' : '' }}>3rd Year</option>
                <option value="4" {{ old('year_avail', $course->year_avail) == '4' ? 'selected' : '' }}>4th Year</option>
            </select>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary">Update Course</button>
    </form>
</div>
@endsection
