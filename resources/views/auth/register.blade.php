@extends('layouts.frontend')

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<style>
    .alert-danger {
        display: none;
    }

    .alert-danger.show {
        display: block;
    }
    .invalid-feedback {
        color: red;
        display: block;
        margin-top: .25rem;
    }
    .form-group {
        margin-bottom: 1rem;
    }
    .form-group label {
        display: block;
        margin-bottom: .5rem;
    }
    .form-group input {
        width: 100%;
    }
    #rfidDisplay {
        margin-top: 1rem;
        font-weight: bold;
    }
    .form-control.is-invalid {
        border-color: #dc3545;
    }
    .form-control.is-invalid:focus {
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }
    .form-control-feedback {
        display: block;
        margin-top: .25rem;
        color: #dc3545;
    }
</style>

<div class="register-container">
    <h2>{{ __('Register') }}</h2>
    <p>Provide your information to get started</p>
    <form method="POST" action="{{ route('register') }}" id="registrationForm">
        @csrf

        <div class="form-group name-group">
            <label for="first_name">{{ __('First Name') }}</label>
            <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" autofocus placeholder="First Name">
            @error('first_name')
                <div class="form-control-feedback">
                    <strong>{{ $message }}</strong>
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="middle_name">{{ __('Middle Name') }}</label>
            <input id="middle_name" type="text" class="form-control @error('middle_name') is-invalid @enderror" name="middle_name" value="{{ old('middle_name') }}" autocomplete="middle_name" placeholder="Middle Name">
            @error('middle_name')
                <div class="form-control-feedback">
                    <strong>{{ $message }}</strong>
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="last_name">{{ __('Last Name') }}</label>
            <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name" placeholder="Last Name">
            @error('last_name')
                <div class="form-control-feedback">
                    <strong>{{ $message }}</strong>
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">{{ __('CSPC Email') }}</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Enter your Institutional Email">
            @error('email')
                <div class="form-control-feedback">
                    <strong>{{ $message }}</strong>
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="phone_number">{{ __('Phone Number') }}</label>
            <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number') }}" required autocomplete="phone_number" placeholder="09*********">
            @error('phone_number')
                <div class="form-control-feedback">
                    <strong>{{ $message }}</strong>
                </div>
            @enderror
        </div>

        <div class="form-group gender-group">
            <label>{{ __('Gender') }}</label><br>
            <input type="radio" id="male" name="gender" value="male" {{ old('gender') == 'male' ? 'checked' : '' }}>
            <label for="male">{{ __('Male') }}</label>
            <input type="radio" id="female" name="gender" value="female" {{ old('gender') == 'female' ? 'checked' : '' }}>
            <label for="female">{{ __('Female') }}</label>
            @error('gender')
                <div class="form-control-feedback">
                    <strong>{{ $message }}</strong>
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="dob">{{ __('Date of Birth') }}</label>
            <input id="dob" type="date" class="form-control @error('date_of_birth') is-invalid @enderror" name="date_of_birth" value="{{ old('date_of_birth') }}" required autocomplete="dob">
            @error('date_of_birth')
                <div class="form-control-feedback">
                    <strong>{{ $message }}</strong>
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">{{ __('Password') }}</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
            @error('password')
                <div class="form-control-feedback">
                    <strong>{{ $message }}</strong>
                </div>
            @enderror
        </div>

        <div class="form-group">
            <label for="password-confirm">{{ __('Confirm Password') }}</label>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
        </div>

        <div class="form-group">
            <button type="button" class="btn btn-primary" id="scanRfidButton">
                {{ __('Scan RFID Card') }}
            </button>
            @error('rfid')
                <div class="form-control-feedback">
                    <strong>{{ $message }}</strong>
                </div>
            @enderror
        </div>

        <div id="rfidDisplay" style="display: none;">
            <p>Scanned RFID: <span id="rfidValue"></span></p>
        </div>

        <div class="form-group">
            <button type="submit" id="registerButton" class="btn btn-success" style="display: none;">
                {{ __('Register') }}
            </button>
        </div>

        <input type="hidden" name="rfid" id="rfid" value="{{ old('rfid') }}">
    </form>
</div>

<div class="modal" id="rfidModal" tabindex="-1" role="dialog" aria-labelledby="rfidModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="rfidModalLabel">{{ __('Scan RFID Card') }}</h5>
                <button type="button" class="close" id="closeModalButton" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p id="scanInstructions">Please scan your RFID card...</p>
                <button id="confirmRfid" class="btn btn-primary">Confirm RFID</button>
                <button id="rescanRfid" class="btn btn-secondary">Rescan</button>
            </div>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/4.0.1/socket.io.js"></script>
<script>
let socket = io('http://172.30.109.177:5000');

socket.on('connect', function() {   
    console.log('Connected to server');
});

socket.on('rfid_status', function(data) {
    console.log('RFID Status:', data);
    $('#scanInstructions').text(data.message);
});

socket.on('rfid_scanned', function(data) {
    console.log('RFID Scanned:', data);
    $('#scanInstructions').hide();
    $('#rfidValue').text(data.rfid);
    $('#rfidDisplay').show();
    $('#registerButton').show();
});

$('#scanRfidButton').click(function() {
    $('#rfidModal').show();
    $('#scanInstructions').show().text('Please scan your RFID card...');
    $('#rfidDisplay').hide();
    $('#registerButton').hide();
    socket.emit('start_scan');
});

$('#confirmRfid').click(function() {
    const rfid = $('#rfidValue').text(); 
    $('#rfid').val(rfid);
    $('#rfidModal').hide();
    socket.emit('stop_scan');
    $('#registrationForm').submit();
});

$('#rescanRfid').click(function() {
    $('#scanInstructions').show().text('Please scan your RFID card...');
    $('#rfidDisplay').hide();
    $('#registerButton').hide();
    socket.emit('start_scan');
});

$('#closeModalButton').click(function() {
    $('#rfidModal').hide();
    socket.emit('stop_scan');
});
</script>
@endsection
@endsection
