@extends('layouts.login')

@section('content')

<div class="login-container">
    <h2>Welcome Back!</h2>
        <form method="POST" action="{{ route('login') }}">
            @csrf
            @if (session('status'))
                <div class="mb-4 font-medium text-sm text-green-600">
                    {{ session('status') }}
                </div>
            @endif
            <div class="form-login-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" class="form-control @error('email') border-red-500 @enderror" placeholder="Enter your email address" required autofocus>
                @error('email')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <div class="form-login-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control @error('password') border-red-500 @enderror" placeholder="Enter your password" required>
                @error('password')
                    <p class="text-red-500 text-xs italic">{{ $message }}</p>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary btn-block">LOGIN</button>
        </form>
        <div class="form-group">
            <a href="{{ route('google-auth') }}" class="google-btn">
                <img src="https://th.bing.com/th/id/R.0fa3fe04edf6c0202970f2088edea9e7?rik=joOK76LOMJlBPw&riu=http%3a%2f%2fpluspng.com%2fimg-png%2fgoogle-logo-png-open-2000.png&ehk=0PJJlqaIxYmJ9eOIp9mYVPA4KwkGo5Zob552JPltDMw%3d&risl=&pid=ImgRaw&r=0" alt="Google Logo">
                Sign in with Google
            </a>
        </div>
</div>

<!-- Modal HTML -->
<div id="register-modal" class="modal" style="display: none;">
    <div class="modal-content">
        <h4>Invalid Login</h4>
        <p>User is not registered yet. Would you like to register?</p>
        <button id="register-yes">Yes, Register</button>
        <button id="register-no">Cancel</button>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        @if (session('register_prompt'))
            // Show the modal if the session indicates user is not registered
            document.getElementById('register-modal').style.display = 'block';

            document.getElementById('register-yes').addEventListener('click', function() {
                // Redirect to the registration page
                window.location.href = '{{ route('register') }}';
            });

            document.getElementById('register-no').addEventListener('click', function() {
                // Redirect back to the login page
                window.location.href = '{{ route('login') }}';
            });
        @endif
    });
</script>

<style>
        /* Modal styling */
    .modal {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5); /* Partly transparent background */
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal-content {
        background: #fff;
        padding: 20px;
        border-radius: 5px;
        text-align: center;
    }

    .modal-content h4 {
        margin-top: 0;
    }

    .modal-content p {
        margin: 10px 0;
    }

    .modal-content button {
        margin: 10px;
        padding: 10px 20px;
        border: none;
        background-color: #ffc107;
        color: #000;
        cursor: pointer;
        border-radius: 4px; /* Added border-radius for better appearance */
        font-size: 16px; /* Added font-size for consistency */
    }

    .modal-content button:hover {
        background-color: #e0a800;
    }

</style>


@endsection
