@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>FACULTY</h2>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))  <!-- Change 'errors' to 'error' -->
    <div class="alert alert-danger">
        {{ session('error') }} 
    </div>
    @endif

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
                    <button type="button" class="btn btn-icon delete" onclick="openDeleteModal('{{ route('admin.faculty.destroy', $faculty->id) }}')">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                    <form action="{{ route('admin.faculty.updateStatus', $faculty->id) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('PUT')
                        <select name="status" onchange="this.form.submit()" 
                            class="status-dropdown {{ $faculty->status == 'Active' ? 'active' : 'disabled' }}">
                            <option value="Active" {{ $faculty->status == 'Active' ? 'selected' : '' }}>Active</option>
                            <option value="Disabled" {{ $faculty->status == 'Disabled' ? 'selected' : '' }}>Disabled</option>
                        </select>
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

<!-- Delete Confirmation Modal -->
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
                Are you sure you want to delete this faculty member? This cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <form id="delete-form" action="" method="POST" style="display:inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function openDeleteModal(actionUrl) {
    document.getElementById('delete-form').action = actionUrl;
    $('#deleteModal').modal('show');
}
</script>
@endsection
