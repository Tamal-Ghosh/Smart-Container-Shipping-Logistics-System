@extends('layouts.app')

@section('title', 'Operator Dashboard — Smart Shipping')

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
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--accent-primary);">⚓ Operator Dashboard</h1>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 4px;">Logistics operations, shipment assignments, and container tracking.</p>
        </div>
    </div>

    <div class="dashboard-card">
        <span class="dashboard-welcome-icon">📦</span>
        <h2>Welcome, Operator</h2>
        <p class="user-info">
            Logged in as <strong>{{ auth()->user()->username ?? 'Operator' }}</strong>
        </p>
        <p style="color: var(--text-secondary); font-size: 0.9rem;">
            You can manage shipments, containers, and track logistics operations from here.
        </p>
    </div>

</div>

@endsection