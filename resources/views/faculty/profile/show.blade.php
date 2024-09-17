@extends('layouts.app')

@section('content')
<div class="container">
    <h2>User Profile Details</h2>
    <div class="profile-info">
        <div class="profile-image">
            <img src="{{ Auth::user()->user_image ? Auth::user()->user_image : asset('images/default_image.jpg') }}" alt="User Image" class="profile-user-image">
        </div>
        <div class="profile-details">
            <div class="profile-detail">
                <strong>Name:</strong>
                <span>{{ strtoupper(Auth::user()->first_name) }} {{ Auth::user()->middle_name ? Auth::user()->middle_name . ' ' : '' }}{{ strtoupper(Auth::user()->last_name) }}</span>
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
    <div class="edit-profile-button">
        <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#editProfileModal">Edit Profile</button>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editProfileModalLabel">Edit Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3 text-center">
                        <img id="profileImagePreview" src="{{ Auth::user()->user_image ? Auth::user()->user_image : asset('images/default_image.jpg') }}" alt="Profile Image" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover;">
                    </div>
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
                    <div class="mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select class="form-select" id="gender" name="gender" required>
                            <option value="male" {{ old('gender', Auth::user()->gender) == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender', Auth::user()->gender) == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender', Auth::user()->gender) == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="date_of_birth" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', Auth::user()->date_of_birth) }}" required>
                    </div>
                    <div class="mb-3">
                        <label for="user_image" class="form-label">Profile Image</label>
                        <input type="file" class="form-control" id="user_image" name="user_image" onchange="previewImage(event)">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
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

<script>
    function previewImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('profileImagePreview');
            output.src = reader.result;
        }
        reader.readAsDataURL(event.target.files[0]);
    }
</script>
@endsection
