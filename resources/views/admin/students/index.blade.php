@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>STUDENTS</h2>
    <div class="action-bar d-flex justify-content-between align-items-center mb-3">
        <div class="search-form-container ml-auto">
            <form method="GET" action="{{ route('admin.students.index') }}" id="search-form" class="d-flex align-items-center">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" id="search-input" placeholder="Search">
                <button type="submit" class="btn btn-secondary ml-2">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
    <table class="custom-table table-bordered">
        <thead>
            <tr>
                <th>Student Number</th>
                <th>Name</th>
                <th>Program & Section</th>
                <th>PC</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
            <tr>
                <td>{{ $student->student_number }}</td>
                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                <td>{{ $student->program }} {{ $student->year }}{{ $student->section }}</td>
                <td>{{ $student->pc_number }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pagination justify-content-center">
        {{ $students->links('vendor.pagination.custom-pagination') }}
    </div>
</div>
@endsection
