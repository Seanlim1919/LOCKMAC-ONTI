@extends('layouts.admin')

@section('content')
<div class="form-container">
    <h2 class="page-title">Faculty Details</h2>
    <form>
        @csrf
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" class="form-control" id="first_name" value="{{ old('first_name', $faculty->first_name) }}" readonly>
                </div>
                <div class="form-group">
                    <label for="middle_name">Middle Name</label>
                    <input type="text" class="form-control" id="middle_name" value="{{ old('middle_name', $faculty->middle_name) }}" readonly>
                </div>
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" class="form-control" id="last_name" value="{{ old('last_name', $faculty->last_name) }}" readonly>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" class="form-control" id="email" value="{{ old('email', $faculty->email) }}" readonly>
                </div>
                <div class="form-group">
                    <label for="phone_number">Phone Number</label>
                    <input type="text" class="form-control" id="phone_number" value="{{ old('phone_number', $faculty->phone_number) }}" readonly>
                </div>

                <div class="form-group">
                    <label for="rfid">RFID ID</label>
                    <input type="text" class="form-control" id="rfid" value="{{ $faculty->rfid ? $faculty->rfid->rfid_code : 'N/A' }}" readonly>
                    <button type="button" class="btn btn-dark" id="scanRfidButton">Scan RFID</button>

                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group d-flex align-items-center">
                    <label class="mr-3">Gender</label>
                    <div class="custom-control custom-radio custom-control-inline">
                        <input type="radio" id="male" name="gender" value="male" class="custom-control-input" {{ old('gender', $faculty->gender) == 'male' ? 'checked' : '' }} disabled>
                        <label class="custom-control-label" for="male">Male</label>
                    </div>
                    <div class="custom-control custom-radio custom-control-inline ml-3">
                        <input type="radio" id="female" name="gender" value="female" class="custom-control-input" {{ old('gender', $faculty->gender) == 'female' ? 'checked' : '' }} disabled>
                        <label class="custom-control-label" for="female">Female</label>
                    </div>
                </div>

                <div class="form-group">
                    <label for="date_of_birth">Date of Birth</label>
                    <input type="date" class="form-control" id="date_of_birth" value="{{ old('date_of_birth', $faculty->date_of_birth) }}" readonly>
                </div>
                
                <div class="form-group">
                    <label>Status</label>
                    <div class="status-text">
                        {{ $faculty->status === 'Active' ? 'Active' : 'Disabled' }}
                    </div>
                </div>

                <div class="form-group">
                    <label for="profile_picture">Profile Picture</label>
                    
                    @if($faculty->profile_picture)
                        <img src="{{ asset('storage/' . $faculty->profile_picture) }}" alt="Current Image" class="current-image" style="width:150px; margin-top:10px;">
                    @elseif($faculty->user_image)
                        <img src= "{{ $faculty->user_image }}" alt="Current Image" class="current-image" style="width:150px; margin-top:10px;">
                    @else
                        <img src="{{ asset('images/default_image.jpg') }}" alt="Default Image" class="current-image" style="width:150px; margin-top:10px;">
                    @endif
                </div>

            </div>
        </div>

        <a href="{{ route('admin.faculty.index') }}" class="btn btn-secondary">Back</a>
    </form>

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
@endsection
