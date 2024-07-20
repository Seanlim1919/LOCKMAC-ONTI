@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>STUDENTS</h2>
    <div class="action-bar d-flex justify-content-between align-items-center mb-3">
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
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pagination justify-content-center">
        {{ $students->links('vendor.pagination.custom-pagination') }}
    </div>
</div>
@endsection
