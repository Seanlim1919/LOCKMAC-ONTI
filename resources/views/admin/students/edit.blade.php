<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    @include('layouts.admin')

    <div class="form-container-wrapper">

        <div class="form-container">

            @if ($errors->any())
            <div class="modal fade" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="errorModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="errorModalLabel">Incorrect/Missing Fields</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <h2 class="text-center">EDIT STUDENT</h2>

            <form class="centered-form" method="POST" action="{{ route('admin.students.update', $student->id) }}" id="registrationForm">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label for="student_number">Student Number</label>
                    <input type="text" class="form-control" id="student_number" name="student_number" value="{{ old('student_number', $student->student_number) }}" required>
                </div>
                <div class="form-group">
                    <label for="firstname">First Name</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" value="{{ old('firstname', $student->first_name) }}" required>
                </div>
                <div class="form-group">
                    <label for="middlename">Middle Name</label>
                    <input type="text" class="form-control" id="middlename" name="middlename" value="{{ old('middlename', $student->middle_name) }}">
                </div>
                <div class="form-group">
                    <label for="lastname">Last Name</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" value="{{ old('lastname', $student->last_name) }}" required>
                </div>
                <div class="form-group">
                    <label for="program">Program</label>
                    <select class="form-control" id="program" name="program" required>
                        <option value="BSIT" {{ old('program', $student->program) == 'BSIT' ? 'selected' : '' }}>BSIT</option>
                        <option value="BLIS" {{ old('program', $student->program) == 'BLIS' ? 'selected' : '' }}>BLIS</option>
                        <option value="BSCS" {{ old('program', $student->program) == 'BSCS' ? 'selected' : '' }}>BSCS</option>
                        <option value="BSIS" {{ old('program', $student->program) == 'BSIS' ? 'selected' : '' }}>BSIS</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="year">Year</label>
                    <select class="form-control" id="year" name="year" required>
                        <option value="1" {{ old('year', $student->year) == '1' ? 'selected' : '' }}>1</option>
                        <option value="2" {{ old('year', $student->year) == '2' ? 'selected' : '' }}>2</option>
                        <option value="3" {{ old('year', $student->year) == '3' ? 'selected' : '' }}>3</option>
                        <option value="4" {{ old('year', $student->year) == '4' ? 'selected' : '' }}>4</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="section">Section</label>
                    <select class="form-control" id="section" name="section" required>
                        <option value="A" {{ old('section', $student->section) == 'A' ? 'selected' : '' }}>A</option>
                        <option value="B" {{ old('section', $student->section) == 'B' ? 'selected' : '' }}>B</option>
                        <option value="C" {{ old('section', $student->section) == 'C' ? 'selected' : '' }}>C</option>
                        <option value="D" {{ old('section', $student->section) == 'D' ? 'selected' : '' }}>D</option>
                        <option value="E" {{ old('section', $student->section) == 'E' ? 'selected' : '' }}>E</option>
                        <option value="F" {{ old('section', $student->section) == 'F' ? 'selected' : '' }}>F</option>
                        <option value="G" {{ old('section', $student->section) == 'G' ? 'selected' : '' }}>G</option>
                        <option value="H" {{ old('section', $student->section) == 'H' ? 'selected' : '' }}>H</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="gender">Gender</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" id="male" value="male" {{ old('gender', strtolower($student->gender)) == 'male' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="male">Male</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="gender" id="female" value="female" {{ old('gender', strtolower($student->gender)) == 'female' ? 'checked' : '' }} required>
                        <label class="form-check-label" for="female">Female</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="pc_number">PC Number</label>
                    <input type="number" class="form-control" id="pc_number" name="pc_number" value="{{ old('pc_number', $student->pc_number) }}" min="1" max="30" required>
                </div>

                <div class="form-group">
                    <label for="rfid">RFID</label>
                    <div class="d-flex align-items-center">
                        <input type="text" class="form-control mr-2" id="rfid" name="rfid" value="{{ old('rfid', $student->rfid ? $student->rfid->rfid_code : '') }}" readonly>
                        <button type="button" class="btn btn-gradient" id="scanRfidButton">Scan RFID</button>
                    </div>
                </div>

                <button type="button" class="btn btn-gradient-submit" id="submitButton">Submit</button>
            </form>
        </div>

        <div class="modal fade" id="rfidModal" tabindex="-1" role="dialog" aria-labelledby="rfidModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="rfidModalLabel">Scan RFID Card</h5>
                        <button type="button" class="close" id="closeModalButton" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p id="scanInstructions">Please scan your RFID card...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.0.1/socket.io.min.js"></script>
    <script>
    $(document).ready(function() {
        @if ($errors->any())
            $('#errorModal').modal('show');
        @endif

        let clientIp = "{{ $clientIp }}";
        let socket = io('http://' + clientIp + ':5000');

        socket.on('connect', function() {
            console.log('Connected to server');
        });

        socket.on('rfid_scanned', function(data) {
            console.log('RFID Scanned:', data);
            let formattedRfid = data.rfid.replace(/\s+/g, '').match(/.{1,2}/g).join(' ');
            $('#rfid').val(formattedRfid);
            $('#rfidModal').modal('hide');
        });

        socket.on('rfid_scan_error', function(message) {
            console.error('RFID Scan Error:', message);
            $('#errorMessage').text(message);
            $('#errorAlert').removeClass('d-none');
            $('#rfidModal').modal('hide');
            socket.emit('stop_scan');
        });

        $('#scanRfidButton').click(function() {
            $('#rfidModal').modal('show');
            $('#scanInstructions').text('Please scan your RFID card...');
            socket.emit('start_scan');
        });

        $('#closeModalButton').click(function() {
            $('#rfidModal').modal('hide');
            socket.emit('stop_scan');
        });

        $('#submitButton').click(function() {
            $('#registrationForm').submit();
        });
    });
    </script>
</body>
</html>
