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
            <span>{{ Auth::user()->first_name }} </span>
            <i class="fas fa-chevron-down dropdown-toggle"></i>
            <div class="dropdown-menu">
                <a href="#" id="settings-link">Settings</a>
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

    <!-- Settings Modal -->
    <div class="modal" id="settingsModal" tabindex="-1" role="dialog" aria-labelledby="settingsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="settingsModalLabel">Settings</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="settingsForm">
                        @csrf
                        <div class="form-group">
                            <label for="first_name">{{ __('First Name') }}</label>
                            <input id="first_name" type="text" class="form-control" name="first_name" required placeholder="First Name">

                            <label for="middle_name">{{ __('Middle Name') }}</label>
                            <input id="middle_name" type="text" class="form-control" name="middle_name" placeholder="Middle Name">

                            <label for="last_name">{{ __('Last Name') }}</label>
                            <input id="last_name" type="text" class="form-control" name="last_name" required placeholder="Last Name">
                        </div>

                        <div class="form-group">
                            <label for="email">{{ __('Email') }}</label>
                            <input id="email" type="email" class="form-control" name="email" required placeholder="Enter your email">
                        </div>

                        <div class="form-group">
                            <label for="phone_number">{{ __('Phone Number') }}</label>
                            <input id="phone_number" type="text" class="form-control" name="phone_number" required placeholder="09*********">
                        </div>

                        <div class="form-group">
                            <label for="password">{{ __('Password') }}</label>
                            <input id="password" type="password" class="form-control" name="password" placeholder="Leave blank to keep current password">
                        </div>

                        <div class="form-group">
                            <label for="password-confirm">{{ __('Confirm Password') }}</label>
                            <input id="password-confirm" type="password" class="form-control" name="password_confirmation">
                        </div>

                        <div class="button-container">
                            <button type="button" class="btn btn-primary" id="saveSettingsButton">Save Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.querySelector('.dropdown-toggle').addEventListener('click', function() {
            document.querySelector('.dropdown-menu').classList.toggle('show');
        });

        document.getElementById('settings-link').addEventListener('click', function(event) {
            event.preventDefault();
            $('#settingsModal').modal('show');

            // Load user data into the form
            $.ajax({
                url: '{{ route("settings.edit") }}',
                method: 'GET',
                success: function(response) {
                    $('#first_name').val(response.user.first_name);
                    $('#middle_name').val(response.user.middle_name);
                    $('#last_name').val(response.user.last_name);
                    $('#email').val(response.user.email);
                    $('#phone_number').val(response.user.phone_number);
                }
            });
        });

        document.getElementById('saveSettingsButton').addEventListener('click', function() {
            const formData = $('#settingsForm').serialize();

            $.ajax({
                url: '{{ route("settings.update") }}',
                method: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        $('#settingsModal').modal('hide');
                        location.reload();
                    } else {
                        // Handle validation errors
                        console.log(response.errors);
                    }
                }
            });
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
