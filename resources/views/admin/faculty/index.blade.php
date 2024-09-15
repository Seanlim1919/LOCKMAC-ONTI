<!-- resources/views/admin/faculty/index.blade.php -->

@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>FACULTY</h2>
    <div class="action-bar d-flex justify-content-between align-items-center mb-3">
        <div class="search-form-container ml-auto">
            <form method="GET" action="{{ route('admin.faculty.index') }}" id="search-form" class="d-flex align-items-center">
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" id="search-input" placeholder="Search">
                <button type="submit" class="btn btn-secondary ml-2">
                    <i class="fas fa-search"></i>
                </button>
            </form>
        </div>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($faculties as $faculty)
            <tr>
                <td>{{ ucwords(strtolower($faculty->first_name . ' ' . $faculty->last_name)) }}</td>
                <td>{{ strtolower($faculty->email) }}</td>
                <td>{{ $faculty->phone_number }}</td>
                <td>
                    <a href="{{ route('admin.faculty.edit', $faculty->id) }}" class="btn btn-icon edit"><i class="fas fa-edit"></i></a>
                    <form action="{{ route('admin.faculty.destroy', $faculty->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-icon delete" onclick="return confirm('Are you sure you want to delete this faculty?');"><i class="fas fa-trash-alt"></i></button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div class="pagination justify-content-center">
        {{ $faculties->links('vendor.pagination.custom-pagination') }}
    </div>
</div>
@endsection
