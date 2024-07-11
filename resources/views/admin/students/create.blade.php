<!-- resources/views/admin/students/create.blade.php -->

@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Add New Student</h2>
    <form method="POST" action="{{ route('admin.students.store') }}">
        @csrf
        <div class="form-group">
            <label for="student_number">Student Number</label>
            <input type="text" class="form-control" id="student_number" name="student_number" required>
        </div>
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" required>
        </div>
        <div class="form-group">
            <label for="middle_name">Middle Name</label>
            <input type="text" class="form-control" id="middle_name" name="middle_name">
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" required>
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
            <label for="gender">Gender</label>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="gender" id="male" value="male" required>
                <label class="form-check-label" for="male">Male</label>
                <input class="form-check-input" type="radio" name="gender" id="female" value="female" required>
                <label class="form-check-label" for="female">Female</label>
            </div>
        </div>
        <div class="form-group">
            <label for="pc_number">PC Number</label>
            <input type="number" class="form-control" id="pc_number" name="pc_number" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Student</button>
    </form>
</div>
@endsection
