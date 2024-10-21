@extends('layouts.app')

@section('content')
<style>
    .btn-container {
        position: relative;
        display: flex;
        justify-content: flex-start; /* Align items to the right */
        align-items: center;
    }

    #search-form {
        display: flex;
        order: 2; /* Ensure the search form appears second (on the right) */
    }

    #filter-button {
        order: 1; /* Ensure the filter button appears first (on the left) */
        margin-right: 10px;
        cursor: pointer;
    }

    #filter-options {
        position: absolute;
        top: 100%;
        right: 0; /* Align the dropdown to the right */
        display: none;
        width: 100%;
        padding: 20px;
        background-color: white;
        border: 1px solid #ddd;
        z-index: 1000;
        max-width: 500px;
    }

    .dropdown-menu.shadow {
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-apply-filter {
        background-color: #007bff;
        color: white;
        border: none;
        margin-top: 10px;
    }

    .btn-apply-filter:hover {
        background-color: #0056b3;
    }

    .action-icon {
        font-size: 20px; /* Adjust size as needed */
        margin: 0 10px; /* Space between icons */
        cursor: pointer; /* Change cursor to pointer */
        transition: color 0.3s; /* Smooth color transition */
    }

    .edit-icon {
        color: #000; /* Initial color for edit icon (black) */
    }

    .delete-icon {
        color: #ff0000; /* Initial color for delete icon (red) */
    }

    .edit-icon:hover {
        color: #007bff; 
    }

    .delete-icon:hover {
        color: #cc0000; /* Darker shade of red on hover */
    }


</style>

<div class="container">
    <h1>STUDENTS</h1>

    @if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
    @endif

    @if(session('errors') && count(session('errors')) > 0)
    <div class="alert alert-danger">
        <p>Oops! An error occurred! Please check the file!</p>
    </div>
    @endif

    <div class="btn-container">
        <a href="{{ route('students.create') }}" class="btn btn-primary">Add Student</a>
        <button class="btn btn-secondary" data-toggle="modal" data-target="#importModal">Import</button>

        <div class="btn-container position-relative" style="margin-left: 350px;">
            <form method="GET" action="{{ route('students.index') }}" id="search-form" class="d-flex align-items-center">
                <input type="text" name="search" value="{{ $search ?? '' }}" class="form-control" id="search-input" placeholder="Search">
                <button type="button" class="btn btn-secondary ml-1" id="filter-button">
                    <i class="fas fa-filter"></i>
                </button>
            </form>

            <div id="filter-options" class="dropdown-menu shadow">
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
                    <button type="submit" class="btn btn-apply-filter">Apply Filters</button>
                </form>
            </div>
        </div>
    </div>

    <table class="table table-bordered mt-3">
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
                <td>{{ ucwords(strtolower($student->first_name . ' ' . $student->last_name)) }}</td>
                <td>{{ $student->program }} {{ $student->year }}{{ $student->section }}</td>
                <td>{{ $student->pc_number }}</td>
                <td>
                    <a href="{{ route('students.edit', $student->id) }}" class="text-dark edit-icon">
                        <i class="fas fa-edit"></i>
                    </a>
                </td>

            </tr>

            <!-- Delete Confirmation Modal -->
            <div class="modal fade" id="confirmDeleteModal{{ $student->id }}" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteModalLabel{{ $student->id }}" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="confirmDeleteModalLabel{{ $student->id }}">Confirm Delete</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            Are you sure you want to delete this student?
                        </div>
                        <div class="modal-footer">
                            <form method="POST" action="{{ route('students.destroy', $student->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-danger">Delete</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </tbody>
    </table>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1" role="dialog" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Import Student List</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="{{ route('students.import') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="file">Select an Excel File</label>
                            <input type="file" class="form-control" id="file" name="file" required>
                        </div>
                        <button type="submit" class="btn btn-submit">Import</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-input');
    const filterButton = document.getElementById('filter-button');
    const filterOptions = document.getElementById('filter-options');

    searchInput.addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); 
            document.getElementById('search-form').submit();
        }
    });

    filterButton.addEventListener('click', function() {
        filterOptions.style.display = filterOptions.style.display === 'none' ? 'block' : 'none';
    });
});

    function readRFID(id) {
        // Implement the RFID reading logic here
        alert('Read RFID for student ID: ' + id);
    }
</script>
@endsection
