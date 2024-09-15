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
            <select name="course_id" id="course_id" class="form-control">
                @foreach ($courses as $course)
                    <option value="{{ $course->id }}" data-program="{{ $course->program }}" {{ $schedule->course_id == $course->id ? 'selected' : '' }}>
                        {{ $course->course_code }} - {{ $course->course_name }}
                    </option>
                @endforeach
            </select>
        </div>
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

        <button type="submit" class="btn btn-primary">Update Schedule</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const programSelect = document.getElementById('program');
    const courseSelect = document.getElementById('course_id');
    const courseOptions = Array.from(courseSelect.options);

    programSelect.addEventListener('change', function () {
        const selectedProgram = this.value;
        
        courseSelect.innerHTML = '';

        courseOptions.forEach(function(option) {
            if (option.getAttribute('data-program') === selectedProgram) {
                courseSelect.appendChild(option);
            }
        });
    });

    programSelect.dispatchEvent(new Event('change'));
});
</script>
@endsection
