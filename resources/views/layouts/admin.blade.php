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

    <!-- Styles -->
    <link href="{{ asset('css/header.css') }}" rel="stylesheet">
    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
</head>
<body>
    <header class="header">
        <div class="left">
            <img src="{{ asset('images/logo.png') }}" alt="LockMac Logo">
            <h1>LockMac</h1>
        </div>
        <div class="right user-info">
            <span>{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</span>
            <i class="fas fa-chevron-down dropdown-toggle"></i>
            <div class="dropdown-menu">
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
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i> Dashboard
        </a>
        <a href="{{ route('admin.faculty.index') }}" class="{{ request()->routeIs('admin.faculty.*') ? 'active' : '' }}">
            <i class="fas fa-user-graduate"></i> Faculty
        </a>
        <a href="{{ route('admin.course.index') }}" class="{{ request()->routeIs('admin.course.*') ? 'active' : '' }}">
            <i class="fas fa-book"></i> Course
        </a>
        <a href="{{ route('admin.students.index') }}" class="{{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
            <i class="fas fa-user-graduate"></i> Students
        </a>
        <a href="#" class="{{ request()->routeIs('attendance.index') ? 'active' : '' }}">
            <i class="fas fa-calendar-check"></i> Attendance
        </a>
        <a href="{{ route('admin.schedule.index') }}" class="{{ request()->routeIs('admin.schedule.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i> Schedule
        </a>
    </aside>

    <script>
        document.querySelector('.dropdown-toggle').addEventListener('click', function() {
            document.querySelector('.dropdown-menu').classList.toggle('show');
        });
    </script>

    <div class="main-content">
        @yield('content')
    </div>
</body>
</html>
