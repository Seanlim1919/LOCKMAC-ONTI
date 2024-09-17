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

@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

<div class="register-container">
    <h2>{{ __('Create Account') }}</h2>
    <form method="POST" action="{{ route('register') }}" id="registrationForm">
        @csrf

        <div class="form-row">
            <div class="form-group">
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
                <input id="middle_name" type="text" class="form-control @error('middle_name') is-invalid @enderror" name="middle_name" value="{{ old('middle_name') }}" autocomplete="middle_name" placeholder="Middle Name (Optional)">
                @error('middle_name')
                    <div class="form-control-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>
        </div>

        <div class="form-row">
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
                <label for="phone_number">{{ __('Phone Number') }}</label>
                <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number') }}" required autocomplete="phone_number" placeholder="09*********">
                @error('phone_number')
                    <div class="form-control-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group gender-group">
                <label for="gender">{{ __('Gender') }}</label>
                <select id="gender" name="gender" class="form-control">
                    <option value="" disabled {{ old('gender') == '' ? 'selected' : '' }}>{{ __('Please select a gender') }}</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>{{ __('Male') }}</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>{{ __('Female') }}</option>
                </select>
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
        </div>

        <div class="form-row">
            <div class="form-group email-group">
                <label for="email">{{ __('CSPC Email') }}</label>
                <div class="input-group">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" 
                        name="email" value="{{ old('email') }}" required autocomplete="email" 
                        placeholder="Enter your Institutional Email">
                </div>
                <button type="button" id="verifyEmailButton" class="btn btn-primary">Verify Email</button>
                @error('email')
                    <div class="form-control-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>

            <div class="form-group otp-group hidden">
                <label for="otp">{{ __('Enter OTP') }}</label>
                <div class="input-container">
                    <input id="otp" type="text" class="form-control" name="otp" placeholder="Enter OTP sent to your email" disabled>
                </div>
                <button type="button" id="submitOtpButton" class="btn btn-primary" disabled>Submit OTP</button>
            </div>
        </div>


        <!-- Password Fields -->
        <div class="form-row">
            <div class="form-group password-group">
                <label for="password">{{ __('Password') }}</label>
                <div class="input-container">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                    <button type="button" id="togglePassword" class="password-toggle"><i class="fa fa-eye"></i></button>
                </div>
                @error('password')
                    <div class="form-control-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>

            <div class="form-group password-group">
                <label for="password-confirm">{{ __('Confirm Password') }}</label>
                <div class="input-container">
                    <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                    <button type="button" id="toggleConfirmPassword" class="password-toggle"><i class="fa fa-eye"></i></button>
                </div>
            </div>
        </div>


        <div class="form-row">
            <div class="form-group">
                <label for="rfid">{{ __('RFID ID') }}</label>
                <input id="rfid" type="text" class="form-control @error('rfid') is-invalid @enderror" name="rfid" value="{{ old('rfid') }}" placeholder="Enter your RFID ID or scan below">
                @error('rfid')
                    <div class="form-control-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                @enderror
            </div>

            <div class="form-group">
                <button type="button" class="btn-register btn-primary" id="scanRfidButton">
                    {{ __('Scan RFID Card') }}
                </button>
            </div>
        </div>

        <div id="rfidDisplay" style="display: none;">
            <p>Scanned RFID: <span id="rfidValue"></span></p>
        </div>

        <div class="form-group">
            <button type="submit" id="registerButton" class="btn btn-success">
                {{ __('Register') }}
            </button>
        </div>
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
<script>
document.addEventListener('DOMContentLoaded', function() {
    let socket = io('http://127.0.0.1:8000');

    socket.on('connect', function() {
        console.log('Connected to server');
    });

    socket.on('rfid_status', function(data) {
        console.log('RFID Status:', data);
        document.getElementById('scanInstructions').textContent = data.message;
    });

    socket.on('rfid_scanned', function(data) {
        console.log('RFID Scanned:', data);
        document.getElementById('scanInstructions').style.display = 'none';
        document.getElementById('rfidValue').textContent = data.rfid;
        document.getElementById('rfidDisplay').style.display = 'block';
        document.getElementById('rfid').value = data.rfid;
        document.getElementById('rfidModal').style.display = 'none';
    });

    document.getElementById('scanRfidButton').addEventListener('click', function() {
        document.getElementById('rfidModal').style.display = 'block';
        document.getElementById('scanInstructions').style.display = 'block';
        document.getElementById('rfidDisplay').style.display = 'none';
        socket.emit('start_scan');
    });

    document.getElementById('confirmRfid').addEventListener('click', function() {
        const rfid = document.getElementById('rfidValue').textContent;
        document.getElementById('rfid').value = rfid;
        document.getElementById('rfidModal').style.display = 'none';
        socket.emit('stop_scan');
    });

    document.getElementById('rescanRfid').addEventListener('click', function() {
        document.getElementById('scanInstructions').style.display = 'block';
        document.getElementById('rfidDisplay').style.display = 'none';
        socket.emit('start_scan');
    });

    document.getElementById('closeModalButton').addEventListener('click', function() {
        document.getElementById('rfidModal').style.display = 'none';
        socket.emit('stop_scan');
    });

    // Toggle Password Visibility
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordField = document.getElementById('password');
        const icon = this.querySelector('i');
        if (passwordField.type === 'password') {
            passwordField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const passwordConfirmField = document.getElementById('password-confirm');
        const icon = this.querySelector('i');
        if (passwordConfirmField.type === 'password') {
            passwordConfirmField.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordConfirmField.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Email Verification
    document.getElementById('verifyEmailButton').addEventListener('click', function() {
        const email = document.getElementById('email').value;

        fetch('/verify-email', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ email })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelector('.otp-group').classList.remove('hidden');
                document.getElementById('otp').disabled = false;
                document.getElementById('submitOtpButton').disabled = false;
                document.getElementById('verifyEmailButton').disabled = true;  // Disable the button after successful OTP send
                alert('OTP has been sent to your email.');
            } else {
                if (data.message === 'Email is already registered') {
                    alert('This email is already registered. Please use a different email.');
                } else {
                    alert(data.message || 'Email verification failed. Please check the email address.');
                }
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('An error occurred. Please try again.');
        });
    });

    // OTP Verification
    document.getElementById('submitOtpButton').addEventListener('click', function() {
        const otp = document.getElementById('otp').value.trim(); 
        const email = document.getElementById('email').value.trim(); 

        fetch('/verify-otp', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ email: email, otp: otp })
        })
        .then(response => response.json())
        .then(data => {
            if (data.valid) {
                document.querySelector('.otp-group').classList.add('hidden');
                document.getElementById('email').disabled = true;
                document.getElementById('verifyEmailButton').disabled = true;  // Disable button after successful verification
                alert('OTP verified successfully.');
            } else {
                alert('Invalid OTP. Please try again.');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            alert('An error occurred. Please try again.');
        });
    });
});
</script>
@endsection
@endsection