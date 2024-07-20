<!-- resources/views/admin/course/index.blade.php -->

@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>COURSES</h2>
    <div class="action-bar d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('admin.course.index') }}" id="search-form" class="d-flex align-items-center">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" id="search-input" placeholder="Search">
            <button type="submit" class="btn btn-secondary ml-2">
                <i class="fas fa-search"></i>
            </button>
        </form>
        <a href="{{ route('admin.course.create') }}" class="btn btn-primary">Add New Course</a>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Program</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($courses as $course)
            <tr>
                <td>{{ $course->course_code }}</td>
                <td>{{ $course->course_name }}</td>
                <td>{{ $course->program }}</td>
                <td>
                    <a href="{{ route('admin.course.edit', $course->id) }}" class="btn btn-icon edit"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.course.destroy', $course->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-icon delete" onclick="return confirm('Are you sure you want to delete this course?');"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pagination justify-content-center">
        {{ $courses->links('vendor.pagination.custom-pagination') }}
    </div>
</div>
@endsection
