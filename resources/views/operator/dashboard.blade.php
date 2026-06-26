@extends('layouts.app')

@section('title', 'Operator Dashboard — Smart Shipping')

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
        <h1>⚓ Operator Dashboard</h1>
        <form method="POST" action="/logout">
            @csrf
            <button type="submit" class="btn-logout">Sign Out</button>
        </form>
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