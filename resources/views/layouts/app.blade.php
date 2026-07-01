<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Smart Container Shipping & Logistics Management System">
    <meta name="theme-color" content="#0f172a">

    <title>@yield('title', 'Smart Shipping & Logistics')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

    @yield('styles')
</head>
<body>

    @auth
        <nav class="main-navbar">
            <div class="navbar-container">
                <div style="display: flex; align-items: center; gap: 16px;">
                    <a href="{{ Auth::user()->role === 'ADMIN' ? '/admin/dashboard' : (Auth::user()->role === 'OPERATOR' ? '/operator/dashboard' : '/customer/dashboard') }}" class="navbar-logo">
                        Smart Shipping
                    </a>
                </div>

                <div class="navbar-links">
                    @if(Auth::user()->role === 'ADMIN')
                        <a href="/ports" class="navbar-link {{ request()->is('ports') || request()->is('ports/*') ? 'active' : '' }}">Ports</a>
                        <a href="/vehicles" class="navbar-link {{ request()->is('vehicles') ? 'active' : '' }}">Vehicles</a>
                        <a href="/containers" class="navbar-link {{ request()->is('containers') ? 'active' : '' }}">Containers</a>
                        <a href="/admin/users" class="navbar-link {{ request()->is('admin/users') ? 'active' : '' }}">Users</a>
                    @elseif(Auth::user()->role === 'OPERATOR')
                        <a href="/operator/shipments/create" class="navbar-link">New Booking</a>
                        <a href="/operator/tracking/log" class="navbar-link">Tracking Log</a>
                    @elseif(Auth::user()->role === 'CUSTOMER')
                        <a href="/tracking" class="navbar-link">Track Shipment</a>
                    @endif
                </div>

                <div class="navbar-user">
                    <div class="user-avatar">
                        {{ strtoupper(substr(Auth::user()->username, 0, 1)) }}
                    </div>
                    <span class="navbar-username">{{ Auth::user()->username }}</span>
                    <form method="POST" action="/logout" style="margin: 0;">
                        @csrf
                        <button type="submit" class="navbar-logout-btn">Sign Out</button>
                    </form>
                </div>
            </div>
        </nav>
    @endauth

    @yield('content')

    <script src="{{ asset('js/auth.js') }}"></script>

    @yield('scripts')
</body>
</html>
