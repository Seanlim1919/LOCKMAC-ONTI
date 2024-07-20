@extends('layouts.frontend')

@section('content')
<div class="register-container">
    <h2>{{ __('Register') }}</h2>
    <p>Provide your information to get started</p>
    <form method="POST" action="{{ route('register') }}" id="registrationForm">
        @csrf

        <!-- Form Fields -->

        <div class="form-group name-group">
            <!-- First Name, Middle Name, Last Name fields -->
            <label for="first_name">{{ __('First Name') }}</label>
            <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" autofocus placeholder="First Name">
            @error('first_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <label for="middle_name">{{ __('Middle Name') }}</label>
            <input id="middle_name" type="text" class="form-control @error('middle_name') is-invalid @enderror" name="middle_name" value="{{ old('middle_name') }}" autocomplete="middle_name" placeholder="Middle Name">
            @error('middle_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <label for="last_name">{{ __('Last Name') }}</label>
            <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name" placeholder="Last Name">
            @error('last_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <!-- Other Form Fields -->

        <div class="form-group">
            <label for="email">{{ __('CSPC Email') }}</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Enter your Institutional Email">
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="phone_number">{{ __('Phone Number') }}</label>
            <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number') }}" required autocomplete="phone_number" placeholder="09*********">
            @error('phone_number')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group gender-group">
            <label>{{ __('Gender') }}</label><br>
            <input type="radio" id="male" name="gender" value="male">
            <label for="male">{{ __('Male') }}</label>
            <input type="radio" id="female" name="gender" value="female">
            <label for="female">{{ __('Female') }}</label>
            @error('gender')
                <br><span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="dob">{{ __('Date of Birth') }}</label>
            <input id="dob" type="date" class="form-control @error('date_of_birth') is-invalid @enderror" name="date_of_birth" value="{{ old('date_of_birth') }}" required autocomplete="dob">
            @error('date_of_birth')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">{{ __('Password') }}</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password-confirm">{{ __('Confirm Password') }}</label>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
        </div>

        <!-- RFID Scan Button -->
        <div class="form-group">
            <button type="button" class="btn btn-primary" id="scanRfidButton">
                {{ __('Scan RFID Card') }}
            </button>
        </div>

    </form>
</div>

<!-- RFID Modal -->
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
                <p>Please scan your RFID card...</p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const rfidModal = document.getElementById('rfidModal');
    const scanRfidButton = document.getElementById('scanRfidButton');
    const closeModalButton = document.getElementById('closeModalButton');

    const rfidHandler = function(event) {
        const rfidValue = event.target.value.trim(); // Assuming RFID scanner inputs value into focused element
        if (rfidValue) {
            // Save RFID to hidden input field or handle as needed
            const rfidField = document.createElement('input');
            rfidField.setAttribute('type', 'hidden');
            rfidField.setAttribute('name', 'rfid');
            rfidField.setAttribute('value', rfidValue);
            document.querySelector('form').appendChild(rfidField);

            // Submit the form
            document.getElementById('registrationForm').submit();
        }
    };

    scanRfidButton.addEventListener('click', function() {
        rfidModal.style.display = 'block';
        document.addEventListener('input', rfidHandler);
    });

    closeModalButton.addEventListener('click', function() {
        rfidModal.style.display = 'none';
        document.removeEventListener('input', rfidHandler);
    });

    window.addEventListener('click', function(event) {
        if (event.target == rfidModal) {
            rfidModal.style.display = 'none';
            document.removeEventListener('input', rfidHandler);
        }
    });
});
</script>
@endsection
