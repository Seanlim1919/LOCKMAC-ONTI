<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7fafc;
            color: #4a5568;
            text-align: center;
        }
        .header {
            background-color: #ffc107;
            padding: 1rem;
            color: black;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
        }
        .header img.logo-left {
            height: 70px;
            width: auto;
        }
        .header img.logo-right {
            height: 70px;
            width: auto;
        }
        .header h1 {
            margin: 0;
            text-align: center;
            position: relative;
            z-index: 1;
        }
        .main-frame {
            display: flex;
            align-items: center;
            justify-content: center;
            height: calc(100vh - 70px);
            padding: 1rem;
            box-sizing: border-box;
        }
        .photo {
            flex: 1;
            display: flex;
            justify-content: center;
            padding: 1rem;
            box-sizing: border-box;
        }
        .photo img {
            max-height: 100%;
            width: auto;
            height: auto;
        }
        .actions {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }
        .actions a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-weight: 800;
            text-decoration: none;
            font-size: 1.2rem;
            margin: 0.75rem 0;
            padding: 0.75rem 1.5rem;
            border-radius: 0.25rem;
            transition: background-color 0.3s, box-shadow 0.3s;
            min-width: 200px;
            text-align: center;
        }
        .actions a.login {
            background: linear-gradient(90deg, #000000 80%, #ffc107 20%);
        }
        .actions a.register {
            background: linear-gradient(90deg, #000000 80%, #ffc107 20%);
        }
        .actions a:hover {
            background: linear-gradient(90deg, #1a202c 20%, #f9c74f 80%);
            opacity: 1;
        }
        .actions a:active {
            background: linear-gradient(90deg, #1a202c 20%, #f9c74f 80%);
        }
        @media (max-width: 768px) {
            .main-frame {
                flex-direction: column;
                text-align: center;
            }
            .photo {
                margin-bottom: 1rem;
                padding: 0;
            }
            .actions {
                margin-top: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="images/cspclogo.png" alt="cspclogo" class="logo-left">
        <h1>LockMac</h1>
        <img src="images/cspcccslogo.png" alt="ccslogo" class="logo-right"> 
    </div>
    <div class="main-frame">
        <div class="photo">
            <img src="images/logo.png" alt="Photo">
        </div>
        <div class="actions">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="dashboard">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="login">Log in</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="register">Register</a>
                    @endif
                @endauth
            @endif
        </div>
    </div>
</body>
</html>
