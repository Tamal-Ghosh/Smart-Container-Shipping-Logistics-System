@extends('layouts.app')

@section('title', 'Sign In — Smart Shipping')

@section('content')

{{-- Animated Background --}}
<div class="auth-bg">
    <div class="grid-overlay"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
    <div class="particle"></div>
</div>
<div class="wave-deco"></div>

<div class="auth-wrapper">
    <div class="auth-card">

        {{-- Branding --}}
        <div class="auth-brand">
            <div class="auth-brand-icon">🚢</div>
            <h1>Smart Shipping</h1>
            <p>Welcome back</p>
        </div>

        {{-- Session Error --}}
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        {{-- Session Success --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Login Form --}}
        <form method="POST" action="/login" class="auth-form">
            @csrf

            {{-- Email --}}
            <div class="form-group">
                <label class="form-label" for="email">Email Address <span class="required">*</span></label>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-input"
                       placeholder="you@example.com"
                       value="{{ old('email') }}"
                       required>
                @error('email')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            {{-- Password --}}
            <div class="form-group">
                <label class="form-label" for="password">Password <span class="required">*</span></label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-input"
                       placeholder="Enter your password"
                       required>
                @error('password')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            {{-- Remember Me --}}
            <label class="form-check">
                <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}>
                <span>Remember me</span>
            </label>

            {{-- Submit --}}
            <button type="submit" class="btn-submit">Sign In</button>
        </form>

        {{-- Footer Link --}}
        <div class="auth-footer">
            <p>Don't have an account? <a href="/register" class="auth-link">Register</a></p>
        </div>

    </div>
</div>

@endsection
