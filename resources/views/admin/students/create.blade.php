<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Student</title>
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
                        <div class="modal-footer">
                            <button type="button" class="btn btn-dark" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            <h2 class="text-center">ADD NEW STUDENT</h2>

            <form class="centered-form" method="POST" action="{{ route('admin.students.store') }}" id="registrationForm">
                @csrf
                <div class="form-group">
                    <label for="student_number">Student Number</label>
                    <input type="text" class="form-control" id="student_number" name="student_number" required>
                </div>
                <div class="form-group">
                    <label for="firstname">First Name</label>
                    <input type="text" class="form-control" id="firstname" name="firstname" required>
                </div>
                <div class="form-group">
                    <label for="middlename">Middle Name</label>
                    <input type="text" class="form-control" id="middlename" name="middlename">
                </div>
                <div class="form-group">
                    <label for="lastname">Last Name</label>
                    <input type="text" class="form-control" id="lastname" name="lastname" required>
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
                    <select class="form-control" id="year" name="year" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="section">Section</label>
                    <select class="form-control" id="section" name="section" required>
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
                    <label for="gender">Gender</label>
                    <select class="form-control" id="gender" name="gender" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>


                <div class="form-group">
                    <label for="pc_number">PC Number</label>
                    <input type="number" class="form-control" id="pc_number" name="pc_number" min="1" max="30" required>
                </div>
                <div class="form-group">
                <label for="rfid">RFID</label>
                <div class="d-flex align-items-center">
                    <input type="text" class="form-control mr-2" id="rfid" name="rfid" readonly>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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

            let formattedRfid = data.rfid.replace(/\s+/g, '');
            formattedRfid = formattedRfid.match(/.{1,2}/g).join(' ');
            $('#rfidValue').text(formattedRfid);
            $('#rfid').val(formattedRfid);
            $('#rfidModal').modal('hide');
            $('#errorAlert').addClass('d-none');
            socket.emit('stop_scan');
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
            $('#scanInstructions').show().text('Please scan your RFID card...');
            $('#rfidDisplay').hide();
            $('#errorAlert').addClass('d-none');
            socket.emit('start_scan');
        });

        $('#closeModalButton').click(function() {
            $('#rfidModal').modal('hide');
            socket.emit('stop_scan');
        });

        $('#confirmRfid').click(function() {
            $('#rfidModal').modal('hide');
        });

        $('#submitButton').click(function() {
            $('#registrationForm').submit();
        });
    });
    </script>
</body>

</html>
