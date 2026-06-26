@extends('layouts.app')

@section('title', 'Customer Dashboard — Smart Shipping')

@section('content')

{{-- Animated Background --}}
<div class="auth-bg">
    <div class="grid-overlay"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
</div>
<div class="wave-deco"></div>

<div class="dashboard-wrapper">

    <div class="dashboard-header">
        <h1>⚓ Customer Dashboard</h1>
        <form method="POST" action="/logout">
            @csrf
            <button type="submit" class="btn-logout">Sign Out</button>
        </form>
    </div>

    <div class="dashboard-card">
        <span class="dashboard-welcome-icon">🚢</span>
        <h2>Welcome aboard!</h2>
        <p class="user-info">
            Logged in as <strong>{{ auth()->user()->username ?? 'Customer' }}</strong>
        </p>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            Track your shipments, manage bookings, and view container statuses from your personal dashboard.
        </p>
    </div>

</div>

@endsection
