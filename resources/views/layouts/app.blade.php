<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LockMac') }}</title>

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap">

    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/student.css') }}" rel="stylesheet">
    <link href="{{ asset('css/faculty.css') }}" rel="stylesheet">
</head>
<body>
<header class="header">
    <div class="left">
        <img src="{{ asset('images/logo.png') }}" alt="LockMac Logo">
        <h1>LockMac</h1>
    </div>
    <div class="right user-info">
        <!-- Display the user's image -->
        @if(Auth::user()->user_image)
            <img src="{{ Auth::user()->user_image }}" alt="{{ Auth::user()->first_name }}'s Image" class="user-image">
        @else
            <img src="{{ asset('images/default_image.jpg') }}" alt="Default Image" class="user-image">
        @endif
        <span>{{ strtoupper(Auth::user()->first_name) }}</span>
        <i class="fas fa-chevron-down dropdown-toggle"></i>
        <div class="dropdown-menu">
            <a href="{{ route('profile.show') }}" id="profile-link">Profile</a>
            <a href="{{ route('logout') }}"
               onclick="event.preventDefault();
                         document.getElementById('logout-form').submit();">
                Logout
            </a>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
        </div>
    </div>
</header>

<aside class="sidebar">
    <a href="{{ route('faculty.dashboard') }}" class="{{ request()->routeIs('faculty.dashboard') ? 'active' : '' }}">
        <i class="fas fa-home"></i> Dashboard
    </a>
    <a href="{{ route('students.index') }}" class="{{ request()->routeIs('students.index') ? 'active' : '' }}">
        <i class="fas fa-user-graduate"></i> Students
    </a>
    <a href="{{ route('faculty.attendance') }}" class="{{ request()->routeIs('faculty.attendance') ? 'active' : '' }}">
        <i class="fas fa-calendar-check"></i> Student Attendance
    </a>
</aside>

<div class="main-content">
    @yield('content')
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
<script>
    document.querySelector('.dropdown-toggle').addEventListener('click', function() {
        document.querySelector('.dropdown-menu').classList.toggle('show');
    });

    // Close the dropdown if the user clicks outside of it
    window.onclick = function(event) {
        if (!event.target.matches('.fas.fa-chevron-down')) {
            const dropdowns = document.getElementsByClassName('dropdown-menu');
            for (let i = 0; i < dropdowns.length; i++) {
                const openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }

    let logoutTimer;

    function resetLogoutTimer() {
        clearTimeout(logoutTimer);
        logoutTimer = setTimeout(() => {
            alert("You have been logged out due to inactivity.");
            document.getElementById('logout-form').submit(); // Submit the logout form
        }, 120000); // 120,000 milliseconds = 2 minutes
    }

    window.onload = resetLogoutTimer;
    document.onmousemove = resetLogoutTimer;
    document.onkeypress = resetLogoutTimer;
</script>
</body>
</html>
