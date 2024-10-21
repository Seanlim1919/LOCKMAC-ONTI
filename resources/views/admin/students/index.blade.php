@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>STUDENTS</h1>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('errors'))
    <div class="alert alert-danger">
        <ul>
            @foreach(session('errors') as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <div class="modal fade" id="deleteConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="deleteConfirmationLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteConfirmationLabel">Confirm Deletion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this student? This action cannot be undone.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-between align-items-center mb-4 position-relative">
        <form method="GET" action="{{ route('admin.students.index') }}" id="search-form" class="d-flex align-items-center flex-grow-1">
            <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" id="search-input" placeholder="Search" style="max-width: 300px;">

            <div class="position-relative" style="margin-left: 10px;">
                <button type="button" class="btn btn-dark" id="filter-button">
                    <i class="fas fa-filter"></i>
                </button>

                <div id="filter-options" class="dropdown-menu mt-2 p-15" style="display: none; position: absolute; top: 100%; left: 0; width: 250px; z-index: 1;">
                    <form method="GET" action="{{ route('admin.students.index') }}" id="filter-form">
                        <div class="form-group">
                            <label for="filter-gender">Gender</label>
                            <select class="form-control" id="filter-gender" name="gender">
                                <option value="">All</option>
                                <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter-program">Program</label>
                            <select class="form-control" id="filter-program" name="program">
                                <option value="">All</option>
                                <option value="BSIT" {{ request('program') == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                                <option value="BLIS" {{ request('program') == 'BLIS' ? 'selected' : '' }}>BLIS</option>
                                <option value="BSCS" {{ request('program') == 'BSCS' ? 'selected' : '' }}>BSCS</option>
                                <option value="BSIS" {{ request('program') == 'BSIS' ? 'selected' : '' }}>BSIS</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter-year">Year</label>
                            <select class="form-control" id="filter-year" name="year">
                                <option value="">All</option>
                                <option value="1" {{ request('year') == '1' ? 'selected' : '' }}>1</option>
                                <option value="2" {{ request('year') == '2' ? 'selected' : '' }}>2</option>
                                <option value="3" {{ request('year') == '3' ? 'selected' : '' }}>3</option>
                                <option value="4" {{ request('year') == '4' ? 'selected' : '' }}>4</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="filter-section">Section</label>
                            <select class="form-control" id="filter-section" name="section">
                                <option value="">All</option>
                                <option value="A" {{ request('section') == 'A' ? 'selected' : '' }}>A</option>
                                <option value="B" {{ request('section') == 'B' ? 'selected' : '' }}>B</option>
                                <option value="C" {{ request('section') == 'C' ? 'selected' : '' }}>C</option>
                                <option value="D" {{ request('section') == 'D' ? 'selected' : '' }}>D</option>
                                <option value="E" {{ request('section') == 'E' ? 'selected' : '' }}>E</option>
                                <option value="F" {{ request('section') == 'F' ? 'selected' : '' }}>F</option>
                                <option value="G" {{ request('section') == 'G' ? 'selected' : '' }}>G</option>
                                <option value="H" {{ request('section') == 'H' ? 'selected' : '' }}>H</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-dark btn-block">Apply Filters</button>
                    </form>
                </div>
            </div>
        </form>

        <div class="position-relative d-flex">
            <button type="button" class="btn btn-gradient-submit" onclick="window.location.href='{{ route('admin.students.create') }}'">
                Add New Student
            </button>
        </div>
    </div>

    <form id="import-excel-form" action="{{ route('admin.students.import') }}" method="POST" enctype="multipart/form-data" style="display: none;">
        @csrf
        <input type="file" name="file" class="form-control-file d-inline-block" required>
        <button type="submit" class="btn btn-dark mt-2">Upload Excel</button>
    </form>

    <form id="import-pdf-form" action="{{ route('admin.students.import-pdf') }}" method="POST" enctype="multipart/form-data" style="display: none;">
        @csrf
        <input type="file" name="file" class="form-control-file d-inline-block" required>
        <button type="submit" class="btn btn-dark mt-2">Upload PDF</button>
    </form>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Student Number</th>
                <th>Name</th>
                <th>Program & Section</th>
                <th>PC Number</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($students as $student)
            <tr>
                <td>{{ $student->student_number }}</td>
                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                <td>{{ $student->program }} {{ $student->year }}{{ $student->section }}</td>
                <td>{{ $student->pc_number }}</td>
                <td class=" align-items-center">
                    <a href="{{ route('admin.students.edit', $student->id) }}" style="background-color: #ffc107; color: rgb(0, 0, 0); border-radius: 4px; margin-right: 10px; text-align: center; text-decoration: none; padding: 5px;">
                        <i class="fas fa-pencil-alt"></i> Edit
                    </a>
                    <form action="{{ route('admin.students.destroy', $student->id) }}" method="POST" onsubmit="return confirmDeletion(this);" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="deletebtn" >
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="pagination justify-content-center">
        {{ $students->appends(request()->input())->links('vendor.pagination.custom-pagination') }}
    </div>
</div>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const filterButton = document.getElementById('filter-button');
        const filterOptions = document.getElementById('filter-options');
        const importButton = document.getElementById('import-button');
        const importOptions = document.getElementById('import-options');
        const importExcelButton = document.getElementById('import-excel-button');
        const importPdfButton = document.getElementById('import-pdf-button');
        const importExcelForm = document.getElementById('import-excel-form');
        const importPdfForm = document.getElementById('import-pdf-form');

        filterButton.addEventListener('click', function() {
            filterOptions.style.display = filterOptions.style.display === 'none' ? 'block' : 'none';
        });

        importButton.addEventListener('click', function() {
            importOptions.style.display = importOptions.style.display === 'none' ? 'block' : 'none';
        });

        importExcelButton.addEventListener('click', function() {
            importExcelForm.style.display = 'block';
            importPdfForm.style.display = 'none';
            importOptions.style.display = 'none';
        });

        importPdfButton.addEventListener('click', function() {
            importPdfForm.style.display = 'block';
            importExcelForm.style.display = 'none';
            importOptions.style.display = 'none';
        });
    });

    let formToDelete; 

    function confirmDeletion(form) {
        formToDelete = form; 
        $('#deleteConfirmationModal').modal('show'); 
        return false; 
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('confirmDeleteButton').addEventListener('click', function() {
            if (formToDelete) {
                formToDelete.submit(); 
            }
        });
    });
</script>
@endsection
