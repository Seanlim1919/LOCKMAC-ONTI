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

        <!-- Hidden RFID Input -->
        <input type="hidden" name="rfid" id="rfid" value="{{ old('rfid') }}">
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

@section('scripts')
<script>
let scanning = false;

$('#scanRfidButton').click(async function() {
    $('#rfidModal').show();
    let scanning = true;

    try {
        // Request USB device
        const device = await navigator.usb.requestDevice({
            filters: [{ vendorId: 0x072f }] // ACR122U Vendor ID
        });
        console.log('Device selected:', device);

        // Open the device if not already opened
        if (!device.opened) {
            await device.open();
            console.log('Device opened');
        } else {
            console.log('Device is already opened');
        }

        // Select the configuration
        await device.selectConfiguration(1); // Ensure correct configuration value
        console.log('Configuration selected');

        // Claim the interface
        await device.claimInterface(0); // Ensure correct interface number
        console.log('Interface claimed');

    } catch (error) {
        console.error('Error interacting with USB device:', error);
    } finally {
        $('#rfidModal').hide();
        scanning = false;
    }
});


</script>


@endsection
