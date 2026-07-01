@extends('layouts.app')

@section('title', 'Sign In — Smart Shipping')

@section('content')

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-brand">
            <h1>Smart Shipping</h1>
            <p>Welcome back</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="/login" class="auth-form">
            @csrf

            <div class="form-group">
                <label class="form-label" for="email">Email Address</label>
                <input type="email"
                       id="email"
                       name="email"
                       class="form-control"
                       placeholder="you@example.com"
                       value="{{ old('email') }}"
                       required>
                @error('email')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control"
                       placeholder="Enter your password"
                       required>
                @error('password')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="btn-auth">Sign In</button>
        </form>

        <div class="auth-footer">
            <p>Don't have an account? <a href="/register" class="auth-link">Register</a></p>
        </div>

    </div>
</div>

@endsection
