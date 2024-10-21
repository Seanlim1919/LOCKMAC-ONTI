@extends('layouts.admin')

@section('content')
<div class="form-container">
    <h2>Add New Schedule</h2>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('admin.schedule.store') }}" method="POST">
        @csrf

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="faculty_id">Faculty</label>
                    <select name="faculty_id" id="faculty_id" class="form-control" required>
                        @foreach ($faculties as $faculty)
                            <option value="{{ $faculty->id }}">{{ ucfirst(strtolower($faculty->first_name)) }} {{ ucfirst(strtolower($faculty->last_name)) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="program">Program</label>
                    <select class="form-control" id="program" name="program" required>
                        <option value="BSIT">BSIT</option>
                        <option value="BLIS">BLIS</option>
                        <option value="BSCS">BSCS</option>
                        <option value="BSIS">BSIS</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="year">Year</label>
                    <select name="year" id="year" class="form-control" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="section">Section</label>
                    <select name="section" id="section" class="form-control" required>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                        <option value="E">E</option>
                        <option value="F">F</option>
                        <option value="G">G</option>
                        <option value="H">H</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="semester_name">Semester</label>
                    <select name="semester_name" id="semester_name" class="form-control" required>
                        <option value="1st Semester">1st Semester</option>
                        <option value="2nd Semester">2nd Semester</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="course_id">Course</label>
                    <select name="course_id" id="course_id" class="form-control" required>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" data-sem="{{ $course->sem_avail }}" data-program="{{ $course->program }}" data-year="{{ $course->year_avail }}">
                                {{ $course->course_code }} - {{ $course->course_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="day">Day</label>
                    <select name="day" id="day" class="form-control" required>
                        <option value="Monday">Monday</option>
                        <option value="Tuesday">Tuesday</option>
                        <option value="Wednesday">Wednesday</option>
                        <option value="Thursday">Thursday</option>
                        <option value="Friday">Friday</option>
                        <option value="Saturday">Saturday</option>
                        <option value="Sunday">Sunday</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="start_time">Start Time</label>
                    <input type="time" name="start_time" id="start_time" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="end_time">End Time</label>
                    <input type="time" name="end_time" id="end_time" class="form-control" required>
                </div>

                <div class="form-group">
                    <label for="year_in">Academic Year</label>
                    <select name="year_in" id="year_in" class="form-control" required>
                    </select>
                </div>

                <div class="form-group">
                    <select name="year_out" id="year_out" class="form-control" required>
                    </select>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Add Schedule</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const yearInSelect = document.getElementById('year_in');
    const yearOutSelect = document.getElementById('year_out');
    const courseSelect = document.getElementById('course_id');
    const programSelect = document.getElementById('program');
    const semesterSelect = document.getElementById('semester_name');
    const yearSelect = document.getElementById('year');

    const currentYear = new Date().getFullYear();  
    const startYear = currentYear - 3;           
    const endYear = currentYear + 5;             

    for (let year = startYear; year <= endYear; year++) {
        const option = document.createElement('option');
        option.value = year;
        option.textContent = year;
        yearInSelect.appendChild(option);
    }

    function populateYearOut(yearIn) {
        yearOutSelect.innerHTML = ''; 
        const minYearOut = parseInt(yearIn) + 1;  
        
        const option = document.createElement('option');
        option.value = minYearOut;
        option.textContent = minYearOut;
        yearOutSelect.appendChild(option);
    }

    yearInSelect.addEventListener('change', function () {
        const selectedYearIn = yearInSelect.value;
        populateYearOut(selectedYearIn);
        yearOutSelect.disabled = false;  
    });

    yearInSelect.value = 2024; 
    populateYearOut(2024);      

    document.querySelector('form').addEventListener('submit', function() {
        yearOutSelect.disabled = false; 
    });

    function filterCoursesBySemesterAndYear() {
        const selectedSemester = semesterSelect.value === '1st Semester' ? 'First' : 'Second';
        const selectedYear = yearSelect.value;

        let firstAvailableCourse = null;
        for (const option of courseSelect.options) {
            const courseYear = option.getAttribute('data-year');
            if (option.getAttribute('data-sem') === selectedSemester && option.getAttribute('data-program') === programSelect.value && courseYear == selectedYear) {
                option.style.display = 'block';
                if (!firstAvailableCourse) {
                    firstAvailableCourse = option;
                }
            } else {
                option.style.display = 'none';
            }
        }

        if (firstAvailableCourse) {
            courseSelect.value = firstAvailableCourse.value;
        } else {
            courseSelect.value = '';
        }
    }

    semesterSelect.addEventListener('change', filterCoursesBySemesterAndYear);
    programSelect.addEventListener('change', filterCoursesBySemesterAndYear);
    yearSelect.addEventListener('change', filterCoursesBySemesterAndYear); // Listen for changes in the year select

    filterCoursesBySemesterAndYear(); // Initial filter
});
</script>

@endsection
