@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>COURSES</h2>

    <div class="action-bar d-flex justify-content-end align-items-center mb-3">
        <form method="GET" action="{{ route('admin.course.index') }}" id="filter-form" class="d-flex align-items-center mr-3">
            <input type="text" name="search" value="{{ request('search') }}" class="form-control" id="search-input" placeholder="Search">
            <select name="sem_avail" id="sem_avail" class="form-control ml-2" onchange="this.form.submit()">
                <option value="">All Semesters</option>
                <option value="First" {{ request('sem_avail') == 'First' ? 'selected' : '' }}>First Semester</option>
                <option value="Second" {{ request('sem_avail') == 'Second' ? 'selected' : '' }}>Second Semester</option>
            </select>
        </form>
        <a href="{{ route('admin.course.create') }}" class="btn btn-quaternary">Add New Course</a>
    </div>

    <table class="custom-table table-bordered">
        <thead>
            <tr>
                <th>Course Code</th>
                <th>Course Name</th>
                <th>Program & Year Available</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($courses as $course)
            <tr>
                <td>{{ $course->course_code }}</td>
                <td>{{ $course->course_name }}</td>
                <td>{{ $course->program }} - {{ $course->year_avail }}{{ ($course->year_avail == 1) ? 'st' : (($course->year_avail == 2) ? 'nd' : (($course->year_avail == 3) ? 'rd' : 'th')) }} Year</td>
                <td>
                    <a href="{{ route('admin.course.edit', $course->id) }}" class="btn btn-icon edit"><i class="fas fa-edit"></i></a>
                    <button class="btn btn-icon delete" data-toggle="modal" data-target="#deleteModal" data-id="{{ $course->id }}"><i class="fas fa-trash-alt"></i></button>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination justify-content-center"> 
        {{ $courses->links('vendor.pagination.custom-pagination') }} 
    </div>
</div>

<!-- Delete Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Confirm Deletion</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this course? This action cannot be undone.
            </div>
            <div class="modal-footer">
            <form id="deleteForm" method="POST" action="{{ route('admin.course.destroy', $course->id) }}">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-danger">Delete</button>
            </form>

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var courseId = button.data('id'); // Extract course ID
        var action = '{{ route('admin.course.destroy', '') }}' + '/' + courseId; // Create the action URL
        $('#deleteForm').attr('action', action); // Update the form action
    });
</script>
@endpush

@endsection
