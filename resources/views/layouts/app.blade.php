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

    <script>
        (function() {
            const theme = localStorage.getItem('theme') || 'light';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark-theme');
            } else {
                document.documentElement.classList.remove('dark-theme');
            }
        })();
    </script>

    @yield('styles')
</head>
<body>

    @auth
        <nav class="main-navbar">
            <div class="navbar-container">
                <a href="{{ Auth::user()->role === 'ADMIN' ? '/admin/dashboard' : (Auth::user()->role === 'OPERATOR' ? '/operator/dashboard' : '/customer/dashboard') }}" class="navbar-logo">
                    Smart Shipping
                </a>

                <div class="navbar-links">
                    @if(Auth::user()->role === 'ADMIN')
                        <a href="/operator/shipments" class="navbar-link {{ request()->is('operator/shipments') && !request()->is('operator/shipments/create') ? 'active' : '' }}">Shipments</a>
                        <a href="/operator/shipments/create" class="navbar-link {{ request()->is('operator/shipments/create') ? 'active' : '' }}">New Booking</a>
                        <a href="/operator/tracking/log" class="navbar-link {{ request()->is('operator/tracking/log') ? 'active' : '' }}">Tracking Log</a>
                        <a href="/ports" class="navbar-link {{ request()->is('ports') || request()->is('ports/*') ? 'active' : '' }}">Ports</a>
                        <a href="/vehicles" class="navbar-link {{ request()->is('vehicles') ? 'active' : '' }}">Vessels/Ships</a>
                        <a href="/containers" class="navbar-link {{ request()->is('containers') ? 'active' : '' }}">Containers</a>
                        <a href="/payments" class="navbar-link {{ request()->is('payments') || request()->is('payments/*') ? 'active' : '' }}">Payments</a>
                        <a href="/admin/users" class="navbar-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">Users</a>
                    @elseif(Auth::user()->role === 'OPERATOR')
                        <a href="/operator/shipments" class="navbar-link {{ request()->is('operator/shipments') && !request()->is('operator/shipments/create') ? 'active' : '' }}">Shipments</a>
                        <a href="/operator/shipments/create" class="navbar-link {{ request()->is('operator/shipments/create') ? 'active' : '' }}">New Booking</a>
                        <a href="/operator/tracking/log" class="navbar-link {{ request()->is('operator/tracking/log') ? 'active' : '' }}">Tracking Log</a>
                        <a href="/ports" class="navbar-link {{ request()->is('ports') || request()->is('ports/*') ? 'active' : '' }}">Ports</a>
                        <a href="/vehicles" class="navbar-link {{ request()->is('vehicles') ? 'active' : '' }}">Vessels/Ships</a>
                        <a href="/containers" class="navbar-link {{ request()->is('containers') ? 'active' : '' }}">Containers</a>
                        <a href="/payments" class="navbar-link {{ request()->is('payments') || request()->is('payments/*') ? 'active' : '' }}">Payments</a>
                    @elseif(Auth::user()->role === 'CUSTOMER')
                        <a href="/operator/shipments" class="navbar-link {{ request()->is('operator/shipments') ? 'active' : '' }}">My Shipments</a>
                        <a href="/tracking" class="navbar-link {{ request()->is('tracking') ? 'active' : '' }}">Track Shipment</a>
                        <a href="/payments" class="navbar-link {{ request()->is('payments') || request()->is('payments/*') ? 'active' : '' }}">My Payments</a>
                    @endif
                </div>

                <div class="navbar-user">
                    <button id="theme-toggle" class="theme-toggle-btn" aria-label="Toggle Theme" type="button">
                        <span class="theme-icon-light">☀️</span>
                        <span class="theme-icon-dark">🌙</span>
                    </button>

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

    @guest
        <button id="theme-toggle-guest" class="theme-toggle-btn guest-toggle" aria-label="Toggle Theme" type="button">
            <span class="theme-icon-light">☀️</span>
            <span class="theme-icon-dark">🌙</span>
        </button>
    @endguest

    @yield('content')

    <script src="{{ asset('js/auth.js') }}"></script>

    <script>
        /* Global clickable-row handler — add data-href="url" to any <tr> to make it navigable */
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('tr[data-href]').forEach(function (row) {
                row.style.cursor = 'pointer';
                row.addEventListener('click', function (e) {
                    // Ignore clicks that originate from interactive elements
                    if (e.target.closest('a, button, input, select, textarea, form, label')) return;
                    window.location.href = row.dataset.href;
                });
                row.addEventListener('mouseenter', function () {
                    row.style.backgroundColor = 'rgba(99, 102, 241, 0.07)';
                    row.style.transition = 'background-color 0.15s ease';
                });
                row.addEventListener('mouseleave', function () {
                    row.style.backgroundColor = '';
                });
            });

            // Theme toggle logic
            const themeToggles = document.querySelectorAll('#theme-toggle, #theme-toggle-guest');
            themeToggles.forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const isDark = document.documentElement.classList.toggle('dark-theme');
                    localStorage.setItem('theme', isDark ? 'dark' : 'light');
                });
            });
        });
    </script>

    @yield('scripts')
</body>
</html>
