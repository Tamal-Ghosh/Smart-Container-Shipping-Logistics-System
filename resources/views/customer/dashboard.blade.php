@extends('layouts.app')

@section('title', 'Customer Dashboard — Smart Shipping')

@section('content')

<div class="auth-bg">
    <div class="grid-overlay"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
</div>
<div class="wave-deco"></div>

<div class="dashboard-wrapper">

    <div class="dashboard-header" style="margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--accent-primary);">⚓ Customer Dashboard</h1>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 4px;">Track shipments, make payments, and manage your shipping requests.</p>
        </div>
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
