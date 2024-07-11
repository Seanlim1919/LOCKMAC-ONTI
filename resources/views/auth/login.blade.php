@extends('layouts.login')

@section('content')

<div class="login-container">
    <h2>Welcome Back!</h2>
    <form method="POST" action="{{ route('login') }}">
        @if (session('status'))
            <div class="mb-4 font-medium text-sm text-green-600">
                {{ session('status') }}
            </div>
        @endif
        @csrf
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" name="email" class="form-control @error('email') border-red-500 @enderror" placeholder="Enter your email address" required autofocus>
            @error('email')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="password" name="password" class="form-control @error('password') border-red-500 @enderror" placeholder="Enter your password" required>
            @error('password')
                <p class="text-red-500 text-xs italic">{{ $message }}</p>
            @enderror
        </div>
        <button type="submit" class="btn btn-primary btn-block">LOG IN</button>
    </form>
</div>

@endsection
