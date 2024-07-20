@extends('layouts.app')

@section('content')
<div class="settings-container">
    <h2>{{ __('SETTINGS') }}</h2>
    <p>Edit your information</p>
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif
    <form method="POST" action="{{ route('settings.update') }}">
        @csrf

        <div class="form-group name-group">
            <label for="first_name">{{ __('First Name') }}</label>
            <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name', $user->first_name) }}" required autocomplete="first_name" autofocus placeholder="First Name">
            @error('first_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <label for="middle_name">{{ __('Middle Name') }}</label>
            <input id="middle_name" type="text" class="form-control @error('middle_name') is-invalid @enderror" name="middle_name" value="{{ old('middle_name', $user->middle_name) }}" autocomplete="middle_name" placeholder="Middle Name">
            @error('middle_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <label for="last_name">{{ __('Last Name') }}</label>
            <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name', $user->last_name) }}" required autocomplete="last_name" placeholder="Last Name">
            @error('last_name')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="email">{{ __('Email') }}</label>
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email" placeholder="Enter your email">
            @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="phone_number">{{ __('Phone Number') }}</label>
            <input id="phone_number" type="text" class="form-control @error('phone_number') is-invalid @enderror" name="phone_number" value="{{ old('phone_number', $user->phone_number) }}" required autocomplete="phone_number" placeholder="09*********">
            @error('phone_number')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password">{{ __('Password') }}</label>
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password" placeholder="Leave blank to keep current password">
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password-confirm">{{ __('Confirm Password') }}</label>
            <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
        </div>

        <div class="button-container">
            <button type="submit" class="btn">
                {{ __('Save Changes') }}
            </button>
        </div>
    </form>
</div>
@endsection
