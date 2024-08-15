@extends('layouts.app')

@section('content')
<div class="form-container">
    <h2>EDIT STUDENT</h2>
    <form class="centered-form" method="POST" action="{{ route('students.update', $student->id) }}">
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
            <label for="year">Year</label>
            <select class="form-control" id="year" name="year" required>
                <option value="1" {{ $student->year == 1 ? 'selected' : '' }}>1</option>
                <option value="2" {{ $student->year == 2 ? 'selected' : '' }}>2</option>
                <option value="3" {{ $student->year == 3 ? 'selected' : '' }}>3</option>
                <option value="4" {{ $student->year == 4 ? 'selected' : '' }}>4</option>
            </select>
        </div>
        <div class="form-group">
            <label for="section">Section</label>
            <select class="form-control" id="section" name="section" required>
                <option value="A" {{ $student->section == 'A' ? 'selected' : '' }}>A</option>
                <option value="B" {{ $student->section == 'B' ? 'selected' : '' }}>B</option>
                <option value="C" {{ $student->section == 'C' ? 'selected' : '' }}>C</option>
                <option value="D" {{ $student->section == 'D' ? 'selected' : '' }}>D</option>
                <option value="E" {{ $student->section == 'E' ? 'selected' : '' }}>E</option>
                <option value="F" {{ $student->section == 'F' ? 'selected' : '' }}>F</option>
                <option value="G" {{ $student->section == 'G' ? 'selected' : '' }}>G</option>
                <option value="H" {{ $student->section == 'H' ? 'selected' : '' }}>H</option>
            </select>
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
        <button type="submit" class="btn btn-submit">Submit</button>
    </form>
</div>
@endsection
