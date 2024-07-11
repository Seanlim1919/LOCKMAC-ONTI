<!-- resources/views/admin/students/index.blade.php -->

@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>STUDENTS</h2>
    <div class="action-bar d-flex justify-content-between align-items-center mb-3">
        <a href="{{ route('admin.students.create') }}" class="btn btn-primary">Add New Student</a>
        <form method="GET" action="{{ route('admin.students.index') }}" id="search-form" class="d-flex align-items-center">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" id="search-input" placeholder="Search">
            <button type="submit" class="btn btn-secondary ml-2">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Student Number</th>
                <th>Year & Section</th>
                <th>PC</th>
                <th>Gender</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
            <tr>
                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                <td>{{ $student->student_number }}</td>
                <td>{{ $student->year_and_section }}</td>
                <td>{{ $student->pc_number }}</td>
                <td>{{ ucfirst($student->gender) }}</td>
                <td>
                    <a href="{{ route('admin.students.edit', $student->id) }}" class="btn btn-icon edit"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-icon delete" onclick="return confirm('Are you sure you want to delete this student?');"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pagination justify-content-center">
        {{ $students->links('vendor.pagination.custom-pagination') }}
    </div>
</div>
@endsection
