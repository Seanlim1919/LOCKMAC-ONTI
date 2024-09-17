<!-- resources/views/faculty/profile/edit.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Edit Profile</h2>
    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <!-- Profile Picture -->
        <div class="form-group">
            <label for="user_image">Profile Image</label>
            <input type="file" class="form-control-file" id="user_image" name="user_image">
            @if(Auth::user()->user_image)
                <img src="{{ Auth::user()->user_image }}" alt="Current Image" class="current-image">
            @else
                <img src="{{ asset('images/default_image.jpg') }}" alt="Current Image" class="current-image">
            @endif
        </div>

        <!-- First Name -->
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" class="form-control" id="first_name" name="first_name" value="{{ Auth::user()->first_name }}" required>
        </div>

        <!-- Middle Name -->
        <div class="form-group">
            <label for="middle_name">Middle Name</label>
            <input type="text" class="form-control" id="middle_name" name="middle_name" value="{{ Auth::user()->middle_name }}">
        </div>

        <!-- Last Name -->
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" class="form-control" id="last_name" name="last_name" value="{{ Auth::user()->last_name }}" required>
        </div>

        <!-- Email -->
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="{{ Auth::user()->email }}" required>
        </div>

        <!-- Phone Number -->
        <div class="form-group">
            <label for="phone_number">Phone Number</label>
            <input type="text" class="form-control" id="phone_number" name="phone_number" value="{{ Auth::user()->phone_number }}" required>
        </div>

        <!-- Password -->
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" class="form-control" id="password" name="password" placeholder="Leave blank to keep current password">
        </div>

        <!-- Confirm Password -->
        <div class="form-group">
            <label for="password_confirmation">Confirm Password</label>
            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
        </div>

        <!-- Save Changes -->
        <button type="submit" class="btn btn-primary">Save Changes</button>
    </form>
</div>
@endsection

@section('styles')
<style>
    .current-image {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        object-fit: cover;
        margin-top: 10px;
    }
</style>
@endsection
