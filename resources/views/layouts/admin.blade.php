<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">



    <title>{{ config('app.name', 'LockMac') }}</title>

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.bundle.min.js"></script>


    <link href="{{ asset('css/admin.css') }}" rel="stylesheet">
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet">
    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">
    <style>

#logout-modal {
    display: none;
    position: fixed;
    z-index: 1050;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    overflow: auto;
    background-color: rgba(0, 0, 0, 0.5);
}
.modal-content {
    background-color: white;
    margin: 15% auto;
    padding: 20px;
    border-radius: 10px;
    width: 80%;
    max-width: 400px;
    text-align: center;
}
#modal-ok-btn {
    padding: 10px 20px;
    background-color: #ffc107;
    color: black;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}
.button-container {
    display: flex;
    justify-content: center;
    margin-top: 20px;
}

#modal-ok-btn:hover {
    background-color: black;
    color: white;

}

    </style>
</head>
<body>
    <header class="header">
        <div class="left">
            <img src="{{ asset('images/logo.png') }}" alt="LockMac Logo">
            <h1>LockMac</h1>
        </div>
        <div class="right user-info">
            @if(Auth::user()->user_image)
                <img src="{{ Auth::user()->user_image }}" alt="{{ Auth::user()->first_name }}'s Image" class="user-image">
            @else
                <img src="{{ asset('images/default_image.jpg') }}" alt="Default Image" class="user-image">
            @endif
            <span>{{ strtoupper(Auth::user()->first_name) }}</span>
            <i class="fas dropdown-toggle"></i>
            <div class="dropdown-menu">
            <a href="{{ route('profiles.show') }}" id="profile-link">Profile</a>
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

    <aside class="sidebar" id="sidebar">
        <a href="#" class="toggle-sidebar" id="toggleSidebar">
            <i class="fas fa-bars"></i> 
        </a>
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i><span class="text">Dashboard</span>
        </a>
        <a href="{{ route('admin.faculty.index') }}" class="{{ request()->routeIs('admin.faculty.*') ? 'active' : '' }}">
            <i class="fas fa-user-graduate"></i><span class="text">Faculty</span>
        </a>
        <a href="{{ route('admin.course.index') }}" class="{{ request()->routeIs('admin.course.*') ? 'active' : '' }}">
            <i class="fas fa-book"></i><span class="text">Course</span>
        </a>
        <a href="{{ route('admin.students.index') }}" class="{{ request()->routeIs('admin.students.*') ? 'active' : '' }}">
            <i class="fas fa-user-graduate"></i><span class="text">Students</span>
        </a>
        <a href="{{ route('admin.attendance') }}" class="{{ request()->routeIs('admin.attendance') ? 'active' : '' }}">
            <i class="fas fa-calendar-check"></i><span class="text">Faculty Logs</span>
        </a>
        <a href="{{ route('admin.schedule.index') }}" class="{{ request()->routeIs('admin.schedule.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt"></i><span class="text">Schedule</span>
        </a>
    </aside>

    <div class="main-content">
        @yield('content')
    </div>

    <div id="logout-modal" class="modal">
    <div class="modal-content">
        <h2>Session Timeout</h2>
        <p>You have been logged out due to inactivity.</p>
        <button id="modal-ok-btn">OK</button>
    </div>
</div>

<form id="logout-form" action="/logout" method="POST" style="display: none;">
    @csrf
</form>


    <script>
        const sidebar = document.getElementById('sidebar');
        const toggleSidebar = document.getElementById('toggleSidebar');

        toggleSidebar.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
        });

        document.querySelector('.dropdown-toggle').addEventListener('click', function() {
            document.querySelector('.dropdown-menu').classList.toggle('show');
        });

        let logoutTimer;
        function resetLogoutTimer() {
    clearTimeout(logoutTimer);
    logoutTimer = setTimeout(() => {
        localStorage.setItem('isInactive', 'true');
        
        document.getElementById('logout-modal').style.display = 'block';
    }, 600000);  
}

document.getElementById('modal-ok-btn').addEventListener('click', function() {
    localStorage.removeItem('isInactive');
    document.getElementById('logout-form').submit();
});

window.addEventListener('load', function() {
    if (localStorage.getItem('isInactive') === 'true') {
        localStorage.removeItem('isInactive');
        document.getElementById('logout-form').submit();
    }
});

        window.onload = resetLogoutTimer;
        document.onmousemove = resetLogoutTimer;
        document.onkeypress = resetLogoutTimer;
    </script>
</body>
</html>
