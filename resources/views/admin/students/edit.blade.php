<!-- resources/views/admin/students/edit.blade.php -->

@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Edit Student</h2>
    <form method="POST" action="{{ route('admin.students.update', $student->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="student_number">Student Number</label>
            <input type="text" class="form-control" id="student_number" name="student_number" value="{{ $student->student_number }}" required>
        </div>
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ $student->first_name }}" required>
        </div>
        <div class="form-group">
            <label for="middle_name">Middle Name</label>
            <input type="text" class="form-control" id="middle_name" name="middle_name" value="{{ $student->middle_name }}">
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ $student->last_name }}" required>
        </div>
        <div class="form-group">
            <label for="program">Program</label>
            <select class="form-control" id="program" name="program" required>
                <option value="BSIT" {{ $student->program == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                <option value="BLIS" {{ $student->program == 'BLIS' ? 'selected' : '' }}>BLIS</option>
                <option value="BSCS" {{ $student->program == 'BSCS' ? 'selected' : '' }}>BSCS</option>
                <option value="BSIS" {{ $student->program == 'BSIS' ? 'selected' : '' }}>BSIS</option>
            </select>
        </div>
        <div class="form-group">
            <label for="year_and_section">Year & Section</label>
            <input type="text" class="form-control" id="year_and_section" name="year_and_section" value="{{ $student->year_and_section }}" required>
        </div>
        <div class="form-group">
            <label for="gender">Gender</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="gender" id="male" value="male" {{ $student->gender == 'male' ? 'checked' : '' }} required>
                <label class="form-check-label" for="male">Male</label>
                <input class="form-check-input" type="radio" name="gender" id="female" value="female" {{ $student->gender == 'female' ? 'checked' : '' }} required>
                <label class="form-check-label" for="female">Female</label>
            </div>
        </div>
        <div class="form-group">
            <label for="pc_number">PC Number</label>
            <input type="number" class="form-control" id="pc_number" name="pc_number" value="{{ $student->pc_number }}" required>
        </div>
        <button type="submit" class="btn btn-primary">Update Student</button>
    </form>
</div>
@endsection
