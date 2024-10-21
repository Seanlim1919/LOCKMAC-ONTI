<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-gradient {
            background: linear-gradient(90deg, #000000 80%, #ffc107 20%);
            color: white;
        }
        .btn-gradient:hover {
            background: linear-gradient(90deg, #1a202c 20%, #f9c74f 80%);
            opacity: 1;
        }
        .btn-gradient:focus {
            box-shadow: none;
        }
        .modal-content {
            border-radius: 0.5rem;
        }
        .modal-header {
            background: linear-gradient(to right, #f9c74f, black);
            color: black;
        }
    </style>
</head>
<body>

    @include('layouts.app') 

    <div class="container mt-4">
        @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="form-container">
            <h2>EDIT STUDENT</h2>

            <div id="errorAlert" class="alert alert-danger d-none" role="alert">
                <span id="errorMessage"></span>
            </div>

            <form class="centered-form" method="POST" action="{{ route('students.update', $student->id) }}" id="registrationForm">
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
                    <select class="form-control" id="gender" name="gender" required>
                        <option value="" disabled>Select Gender</option>
                        <option value="male" {{ old('gender', strtolower($student->gender)) == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender', strtolower($student->gender)) == 'female' ? 'selected' : '' }}>Female</option>
                    </select>
                </div>


                <div class="form-group">
                    <label for="pc_number">PC Number</label>
                    <input type="number" class="form-control" id="pc_number" name="pc_number" value="{{ old('pc_number', $student->pc_number) }}" min="1" max="30" required>
                </div>
                <div class="form-group">
                    <label for="rfid">RFID</label>
                    <div class="input-group">
                        <input type="text" class="form-control" id="rfid" name="rfid" value="{{ old('rfid', $student->rfid ? $student->rfid->rfid_code : '') }}" readonly>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-gradient" id="scanRfidButton">Scan RFID</button>
                        </div>
                    </div>
                </div>
                <button type="button" class="btn btn-gradient" id="submitButton">Submit</button>
            </form>
        </div>

        <div id="rfidModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 1000;">
            <div class="modal-content" style="background: white; margin: auto; padding: 20px; width: 300px; position: relative; top: 25%;">
                <div class="modal-header">
                    <h5 class="modal-title">Scan RFID Card</h5>
                    <button type="button" class="close" id="closeModalButton" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <br>
                <div class="modal-body">
                    <p id="scanInstructions">Please scan your RFID card...</p>
                    <p id="rfidDisplay" style="display:none;">
                        Scanned RFID: <span id="rfidValue"></span>
                    </p>
                    <button id="confirmRfid" class="btn btn-gradient">Confirm RFID</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.0.1/socket.io.min.js"></script>
    <script>
$(document).ready(function() {
    let socket = io('http://172.30.153.104:5000');

    socket.on('connect', function() {
        console.log('Connected to server');
    });

    socket.on('rfid_scanned', function(data) {
        console.log('RFID Scanned:', data);

        let formattedRfid = data.rfid.replace(/\s+/g, '');
        formattedRfid = formattedRfid.match(/.{1,2}/g).join(' ');

        $('#rfidValue').text(formattedRfid);
        $('#rfid').val(formattedRfid);
        $('#rfidModal').hide();
        $('#errorAlert').addClass('d-none');
        socket.emit('stop_scan');
    });

    socket.on('rfid_scan_error', function(message) {
        console.error('RFID Scan Error:', message);
        $('#errorMessage').text(message);
        $('#errorAlert').removeClass('d-none');
        $('#rfidModal').hide();
        socket.emit('stop_scan');
    });

    $('#scanRfidButton').click(function() {
        console.log('Scan RFID button clicked');
        $('#rfidModal').show();
        $('#scanInstructions').show().text('Please scan your RFID card...');
        $('#rfidDisplay').hide();
        $('#errorAlert').addClass('d-none');
        socket.emit('start_scan');
    });

    $('#closeModalButton').click(function() {
        console.log('Close Modal button clicked');
        $('#rfidModal').hide();
        $('#errorAlert').addClass('d-none');
        socket.emit('stop_scan');
    });

    $('#submitButton').click(function() {
        $('#registrationForm').submit();
    });
});
    </script>
</body>
</html>
