@extends('layouts.admin')

@section('content')
<div class="form-container">
    <h2>Edit Schedule</h2>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <form action="{{ route('admin.schedule.update', $schedule->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="faculty_id">Faculty</label>
                    <select name="faculty_id" id="faculty_id" class="form-control">
                        @foreach ($faculties as $faculty)
                            <option value="{{ $faculty->id }}" {{ $schedule->faculty_id == $faculty->id ? 'selected' : '' }}>
                                {{ ucfirst(strtolower($faculty->first_name)) }} {{ ucfirst(strtolower($faculty->last_name)) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="program">Program</label>
                    <select class="form-control" id="program" name="program" required>
                        <option value="BSIT" {{ $schedule->program == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                        <option value="BLIS" {{ $schedule->program == 'BLIS' ? 'selected' : '' }}>BLIS</option>
                        <option value="BSCS" {{ $schedule->program == 'BSCS' ? 'selected' : '' }}>BSCS</option>
                        <option value="BSIS" {{ $schedule->program == 'BSIS' ? 'selected' : '' }}>BSIS</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="year">Year</label>
                    <select name="year" id="year" class="form-control" required>
                        <option value="1" {{ $schedule->year == 1 ? 'selected' : '' }}>1</option>
                        <option value="2" {{ $schedule->year == 2 ? 'selected' : '' }}>2</option>
                        <option value="3" {{ $schedule->year == 3 ? 'selected' : '' }}>3</option>
                        <option value="4" {{ $schedule->year == 4 ? 'selected' : '' }}>4</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="section">Section</label>
                    <select name="section" id="section" class="form-control" required>
                        <option value="A" {{ $schedule->section == 'A' ? 'selected' : '' }}>A</option>
                        <option value="B" {{ $schedule->section == 'B' ? 'selected' : '' }}>B</option>
                        <option value="C" {{ $schedule->section == 'C' ? 'selected' : '' }}>C</option>
                        <option value="D" {{ $schedule->section == 'D' ? 'selected' : '' }}>D</option>
                        <option value="E" {{ $schedule->section == 'E' ? 'selected' : '' }}>E</option>
                        <option value="F" {{ $schedule->section == 'F' ? 'selected' : '' }}>F</option>
                        <option value="G" {{ $schedule->section == 'G' ? 'selected' : '' }}>G</option>
                        <option value="H" {{ $schedule->section == 'H' ? 'selected' : '' }}>H</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="course_id">Course</label>
                    <select name="course_id" id="course_id" class="form-control" required>
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}" data-sem="{{ $course->sem_avail }}" data-program="{{ $course->program }}" data-year="{{ $course->year_available }}" {{ $schedule->course_id == $course->id ? 'selected' : '' }}>
                                {{ $course->course_code }} - {{ $course->course_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="semester_name">Semester</label>
                    <select name="semester_name" id="semester_name" class="form-control" required>
                        <option value="1st Semester" {{ $schedule->semester->semester_name == '1st Semester' ? 'selected' : '' }}>1st Semester</option>
                        <option value="2nd Semester" {{ $schedule->semester->semester_name == '2nd Semester' ? 'selected' : '' }}>2nd Semester</option>
                    </select>
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label for="day">Day</label>
                    <select name="day" id="day" class="form-control">
                        <option value="Monday" {{ $schedule->day == 'Monday' ? 'selected' : '' }}>Monday</option>
                        <option value="Tuesday" {{ $schedule->day == 'Tuesday' ? 'selected' : '' }}>Tuesday</option>
                        <option value="Wednesday" {{ $schedule->day == 'Wednesday' ? 'selected' : '' }}>Wednesday</option>
                        <option value="Thursday" {{ $schedule->day == 'Thursday' ? 'selected' : '' }}>Thursday</option>
                        <option value="Friday" {{ $schedule->day == 'Friday' ? 'selected' : '' }}>Friday</option>
                        <option value="Saturday" {{ $schedule->day == 'Saturday' ? 'selected' : '' }}>Saturday</option>
                        <option value="Sunday" {{ $schedule->day == 'Sunday' ? 'selected' : '' }}>Sunday</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="start_time">Start Time</label>
                    <input type="time" name="start_time" id="start_time" class="form-control" value="{{ $schedule->start_time ? $schedule->start_time->format('H:i') : '' }}" required>
                </div>
                <div class="form-group">
                    <label for="end_time">End Time</label>
                    <input type="time" name="end_time" id="end_time" class="form-control" value="{{ $schedule->end_time ? $schedule->end_time->format('H:i') : '' }}" required>
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

        <button type="submit" class="btn btn-primary">Update Schedule</button>
    </form>
</div>

<script>
function filterCoursesBySemesterAndYear() {
    const selectedSemester = semesterSelect.value; // '1st Semester' or '2nd Semester'
    const selectedYear = yearSelect.value; // Selected year level

    let firstAvailableCourse = null;
    for (const option of courseSelect.options) {
        // Get course attributes
        const courseYear = option.getAttribute('data-year');
        const courseSem = option.getAttribute('data-sem');
        const courseProgram = option.getAttribute('data-program');

        // Check if the course matches selected criteria
        if (courseSem === selectedSemester && courseProgram === programSelect.value && courseYear == selectedYear) {
            option.style.display = 'block'; // Show matching courses
            if (!firstAvailableCourse) {
                firstAvailableCourse = option; // Store first available course
            }
        } else {
            option.style.display = 'none'; // Hide non-matching courses
        }
    }

    // Select the first available course or reset if none found
    courseSelect.value = firstAvailableCourse ? firstAvailableCourse.value : '';
}

// Add event listeners
semesterSelect.addEventListener('change', filterCoursesBySemesterAndYear);
programSelect.addEventListener('change', filterCoursesBySemesterAndYear);
yearSelect.addEventListener('change', filterCoursesBySemesterAndYear);

// Call filter function on load
filterCoursesBySemesterAndYear(); 

</script>

@endsection
