@extends('layouts.admin')

@section('content')
<div class="container">
    <h2 class="page-title">SCHEDULE</h2>
    <div class="action-bar d-flex justify-content-between align-items-center mb-3">
        <form method="GET" action="{{ route('admin.schedule.index') }}" id="scheduleForm" class="d-flex align-items-center">
            <select name="semester" id="semester" class="form-control mr-2" style="width: 150px; height: 40px;">
                <option value="1st" {{ request('semester', '1st') == '1st' ? 'selected' : '' }}>1st Semester</option>
                <option value="2nd" {{ request('semester') == '2nd' ? 'selected' : '' }}>2nd Semester</option>
            </select>

            <select name="year_in" id="year_in" class="form-control mr-2" style="width: 100px; height: 40px;">
                <option value="">Year In</option>
                @for ($year = date('Y') - 3; $year <= date('Y') + 5; $year++)
                    <option value="{{ $year }}" {{ request('year_in', date('Y')) == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endfor
            </select>

            <select name="year_out" id="year_out" class="form-control mr-2" style="width: 110px; height: 40px;">
            <option value="">Year Out</option>
            @if(request('year_in'))
                @for ($year = request('year_in') + 1; $year <= request('year_in') + 1; $year++)
                    <option value="{{ $year }}" {{ request('year_out') == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endfor
            @else
                @for ($year = date('Y'); $year <= date('Y') + 1; $year++)
                    <option value="{{ $year }}" {{ request('year_out', date('Y') + 1) == $year ? 'selected' : '' }}>{{ $year }}</option>
                @endfor
            @endif
        </select>

            
        </form>

        <div class="ml-2 d-flex">
            <a href="{{ route('admin.schedule.exportPdf', ['semester' => request('semester'), 'year_in' => request('year_in'), 'year_out' => request('year_out')]) }}" 
            class="btn btn-quaternary" style="height: 40px; margin-left: 5px;">Export</a>

            <a href="{{ route('admin.schedule.create') }}" class="btn btn-gradient" style="height: 40px; margin-left: 5px; width: auto;">Add New Schedule</a>
        </div>
    </div>

        <form action="{{ route('admin.schedule.useAll') }}" method="POST" class="mb-3">
            @csrf
            <!-- <button type="submit" class="btn btn-dark" style=" width: 150px;">View Schedule</button> -->
            <input type="hidden" name="semester" value="{{ request('semester', '1st') }}">
            <input type="hidden" name="year_in" value="{{ request('year_in', date('Y')) }}">
            <input type="hidden" name="year_out" value="{{ request('year_out', date('Y') + 1) }}">
            <button type="submit" class="btn btn-success">Use Schedule</button>
        </form>


    <div class="info-box-content">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Time</th>
                        <th>M</th>
                        <th>T</th>
                        <th>W</th>
                        <th>Th</th>
                        <th>F</th>
                        <th>Sat</th>
                        <th>S</th>
                    </tr>
                </thead>
                <tbody>
                    @for ($hour = 7; $hour <= 18; $hour++)
                        <tr>
                            <td>{{ formatTime($hour) }} - {{ formatTime($hour + 1) }}</td>
                            @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $day)
                                @php
                                    $scheduleForHour = $schedules->first(function($schedule) use ($day, $hour) {
                                        $startHour = (int) substr($schedule->start_time, 0, 2);
                                        $endHour = (int) substr($schedule->end_time, 0, 2);
                                        return $schedule->day == $day && $hour >= $startHour && $hour < $endHour;
                                    });
                                @endphp
                                @if ($scheduleForHour && $hour == (int) substr($scheduleForHour->start_time, 0, 2))
                                    @php
                                        $startHour = (int) substr($scheduleForHour->start_time, 0, 2);
                                        $endHour = (int) substr($scheduleForHour->end_time, 0, 2);
                                        $rowspan = $endHour - $startHour;
                                    @endphp
                                    <td class="time-slot occupied" rowspan="{{ $rowspan }}"
                                        onclick="window.location='{{ route('admin.schedule.edit', $scheduleForHour->id) }}'"
                                        data-toggle="tooltip" data-placement="top"
                                        title="{{ $scheduleForHour->course_name }} with {{ getFacultyTitle($scheduleForHour->faculty) }} {{ $scheduleForHour->faculty->first_name }} {{ $scheduleForHour->faculty->last_name }}">
                                        <div>
                                            <div>
                                                {{ $scheduleForHour->course_code }}<br>
                                                {{ strtoupper(getFacultyTitle($scheduleForHour->faculty)) }} {{ strtoupper($scheduleForHour->faculty->last_name) }}<br>
                                                {{ $scheduleForHour->program }} - {{ $scheduleForHour->year }}{{ $scheduleForHour->section }}
                                            </div>
                                            <div class="actions">
                                            <button type="button" class="btn btn-icon delete" data-toggle="modal" data-target="#deleteModal" 
                                                    onclick="event.preventDefault(); document.getElementById('deleteForm').setAttribute('action', '{{ route('admin.schedule.destroy', $scheduleForHour->id) }}');">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            </div>
                                        </div>
                                    </td>
                                @else
                                    <td class="time-slot"></td>
                                @endif
                            @endforeach
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>
    </div>
    <!-- Modal -->
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
                    Are you sure you want to delete this schedule?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <form id="deleteForm" action="" method="POST" style="display: inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('#deleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget); // Button that triggered the modal
        var actionUrl = button.data('action'); // Extract info from data-* attributes
        var modal = $(this);
        
        modal.find('#deleteForm').attr('action', actionUrl);
    });
document.getElementById('year_in').addEventListener('change', function() {
    var yearIn = parseInt(this.value);
    if (!isNaN(yearIn)) {
        var yearOutSelect = document.getElementById('year_out');
        
        yearOutSelect.innerHTML = '<option value="">Year Out</option>';
        
        for (var year = yearIn + 1; year <= yearIn + 1; year++) {
            var option = document.createElement('option');
            option.value = year;
            option.text = year;
            yearOutSelect.appendChild(option);
        }

        yearOutSelect.value = yearIn + 1; 
    }
    document.getElementById('scheduleForm').submit(); 
    });

    document.getElementById('semester').addEventListener('change', function() {
        document.getElementById('scheduleForm').submit();
    });

    document.getElementById('year_out').addEventListener('change', function() {
        document.getElementById('year_out_display').value = this.value;
        document.getElementById('scheduleForm').submit(); 
    });
</script>

@endsection
