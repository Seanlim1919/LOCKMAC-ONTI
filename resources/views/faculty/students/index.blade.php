@extends('layouts.app')

@section('content')
<div class="container">
    <h2>STUDENTS</h2>
    <div class="action-bar d-flex justify-content-end align-items-center position-relative">
        <form method="GET" action="{{ route('students.index') }}" id="search-form" class="d-flex align-items-center">
            <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" id="search-input" placeholder="Search">
            <button type="button" class="btn btn-secondary ml-2" id="filter-button">
                <i class="fas fa-filter"></i>
            </button>
        </form>
        <a href="{{ route('students.create') }}" class="btn btn-primary ml-2"><i class="fas fa-plus"></i> Add New Student</a>
        <button type="button" class="btn btn-primary ml-2" id="import-button">
            <i class="fas fa-upload"></i> Import Student List
        </button>
    </div>

    <div id="filter-options" class="dropdown-menu position-absolute" style="display: none;">
        <form method="GET" action="{{ route('students.index') }}" id="filter-form">
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
                <label for="filter-year-and-section">Year & Section</label>
                <input type="text" class="form-control" id="filter-year-and-section" name="year_and_section" value="{{ request('year_and_section') }}">
            </div>
            <button type="submit" class="btn btn-apply-filter">Apply Filters</button>
        </form>
    </div>

    <div id="import-options" class="dropdown-menu position-absolute" style="display: none;">
        <button type="button" class="btn btn-secondary" id="import-pdf-button">
            Import PDF
        </button>
        <button type="button" class="btn btn-secondary" id="import-excel-button">
            Import Excel
        </button>
    </div>

    <form id="import-excel-form" action="{{ route('students.import') }}" method="POST" enctype="multipart/form-data" style="display: none;">
        @csrf
        <input type="file" name="file" class="form-control-file d-inline-block" required>
        <button type="submit" class="btn btn-success ml-2">Upload Excel</button>
    </form>

    <form id="import-pdf-form" action="{{ route('students.import-pdf') }}" method="POST" enctype="multipart/form-data" style="display: none;">
        @csrf
        <input type="file" name="file" class="form-control-file d-inline-block" required>
        <button type="submit" class="btn btn-danger ml-2">Upload PDF</button>
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
                <td>{{ $student->program }} {{ $student->year_and_section }}</td>
                <td>{{ $student->pc_number }}</td>
                <td class="actions">
                    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-icon edit">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('students.destroy', $student->id) }}" method="POST" class="d-inline-block">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-icon delete">
                            <i class="fas fa-trash-alt"></i>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const importButton = document.getElementById('import-button');
        const importOptions = document.getElementById('import-options');
        const importExcelButton = document.getElementById('import-excel-button');
        const importPdfButton = document.getElementById('import-pdf-button');
        const importExcelForm = document.getElementById('import-excel-form');
        const importPdfForm = document.getElementById('import-pdf-form');

        importButton.addEventListener('click', function () {
            importOptions.style.display = importOptions.style.display === 'none' ? 'block' : 'none';
        });

        importExcelButton.addEventListener('click', function () {
            importExcelForm.style.display = 'block';
            importPdfForm.style.display = 'none';
            importOptions.style.display = 'none';
        });

        importPdfButton.addEventListener('click', function () {
            importPdfForm.style.display = 'block';
            importExcelForm.style.display = 'none';
            importOptions.style.display = 'none';
        });

        const searchInput = document.getElementById('search-input');
        const filterButton = document.getElementById('filter-button');
        const filterOptions = document.getElementById('filter-options');
        let debounceTimeout;

        searchInput.addEventListener('input', function () {
            clearTimeout(debounceTimeout);
            debounceTimeout = setTimeout(function () {
                document.getElementById('search-form').submit();
            }, 300); // Adjust the debounce delay as needed
        });

        filterButton.addEventListener('click', function () {
            filterOptions.style.display = filterOptions.style.display === 'none' ? 'block' : 'none';
        });
    });
</script>
@endsection
