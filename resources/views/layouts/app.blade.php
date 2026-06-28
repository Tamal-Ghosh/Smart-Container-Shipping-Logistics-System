<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Smart Container Shipping & Logistics Management System">
    <meta name="theme-color" content="#0f172a">

    <title>@yield('title', 'Smart Shipping & Logistics')</title>

    {{-- Google Fonts — Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Global Styles --}}
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

    @yield('styles')
</head>
<body>

    @auth
        <nav class="main-navbar">
            <div class="navbar-container">
                <a href="#" class="navbar-logo">🚢 Smart Shipping</a>
                <div class="navbar-menu">
                    <span class="role-badge">{{ Auth::user()->role }} Portal</span>
                </div>
                <div class="navbar-user">
                    <span class="navbar-username">👤 {{ Auth::user()->username }}</span>
                    <form method="POST" action="/logout" style="margin: 0;">
                        @csrf
                        <button type="submit" class="navbar-logout-btn">Sign Out</button>
                    </form>
                </div>
            </div>
        </nav>
    @endauth

    @yield('content')

    {{-- Global Scripts --}}
    <script src="{{ asset('js/auth.js') }}"></script>

    @yield('scripts')
</body>
</html>
