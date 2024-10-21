@extends('layouts.admin')

@section('content')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .modal-dialog.modal-lg {
            max-width: 80vw; 
            margin: 1.75rem auto;
        }
        .modal-content {
            border-radius: 1rem; 
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); 
        }
        .modal-header {
            border-bottom: none; 
            background-color: #f8f9fa; 
        }
        .modal-body {
            padding: 2rem; 
        }
        .modal-footer {
            border-top: none; 
        }
        .form-control, .form-select {
            border-radius: 0.5rem; 
            box-shadow: none; 
        }
        .btn {
            border-radius: 0.5rem;
        }
        #profileImagePreview {
            width: 150px; 
            height: 150px;
            object-fit: cover;
        }
        .form-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Admin Profile Details</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="profile-info">
            <div class="profile-image">
                <img src="{{ Auth::user()->user_image ? Auth::user()->user_image : asset('images/default_image.jpg') }}" alt="User Image" class="profile-user-image rounded-circle" style="width: 150px; height: 150px;">
            </div>
            <div class="profile-details mt-3">  
                <div class="profile-detail">
                    <strong>Name:</strong>
                    <span>{{ (Auth::user()->first_name) }} {{ Auth::user()->middle_name ? Auth::user()->middle_name . ' ' : '' }}{{ (Auth::user()->last_name) }}</span>
                </div>
                <div class="profile-detail">
                    <strong>Email:</strong>
                    <span>{{ strtolower(Auth::user()->email) }}</span>
                </div>
                <div class="profile-detail">
                    <strong>Phone Number:</strong>
                    <span>{{ Auth::user()->phone_number }}</span>
                </div>
                <div class="profile-detail">
                    <strong>Gender:</strong>
                    <span>{{ ucfirst(Auth::user()->gender) }}</span>
                </div>
                <div class="profile-detail">
                    <strong>Date of Birth:</strong>
                    <span>{{ Auth::user()->date_of_birth ? \Carbon\Carbon::parse(Auth::user()->date_of_birth)->format('F d, Y') : 'N/A' }}</span>
                </div>
            </div>
        </div>
        <div class="edit-profile-button mt-3">
            <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
        </div>
    </div>

    <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('profiles.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="modal-body">
                        <div class="mb-3 text-center">
                            <img id="profileImagePreview" src="{{ Auth::user()->user_image ? Auth::user()->user_image : asset('images/default_image.jpg') }}" alt="Profile Image" class="rounded-circle">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" value="{{ old('first_name', Auth::user()->first_name) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="middle_name" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="middle_name" name="middle_name" value="{{ old('middle_name', Auth::user()->middle_name) }}">
                                </div>
                                <div class="mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" value="{{ old('last_name', Auth::user()->last_name) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ old('phone_number', Auth::user()->phone_number) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="male" {{ old('gender', Auth::user()->gender) == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender', Auth::user()->gender) == 'female' ? 'selected' : '' }}>Female</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="date_of_birth" class="form-label">Date of Birth</label>
                                    <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', Auth::user()->date_of_birth) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="password" name="password">
                                </div>
                                <div class="mb-3">
                                    <label for="password_confirmation" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                                </div>
                                <div class="mb-3">
                                    <label for="rfid" class="form-label">RFID</label>
                                    <input type="text" class="form-control" id="rfid" name="rfid" value="{{ old('rfid', Auth::user()->rfid->rfid_code) }}" readonly>
                                </div>
                                <div class="mb-3">
                                    <button type="button" class="btn btn-outline-primary" id="scanRfidButton">Scan RFID</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                </form>
            </div>
        </div>
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
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/2.11.6/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.0.1/socket.io.min.js"></script>
    <script>
        function previewImage(event) {
            const reader = new FileReader();
            reader.onload = function() {
                const output = document.getElementById('profileImagePreview');
                output.src = reader.result;
            }
            reader.readAsDataURL(event.target.files[0]);
        }

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
@endsection
