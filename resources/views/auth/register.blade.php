@extends('layouts.app')

@section('title', 'Create Account — Smart Shipping')

@section('content')

<div class="auth-wrapper">
    <div class="auth-card">

        <div class="auth-brand">
            <h1>Smart Shipping</h1>
            <p>Create your account</p>
        </div>

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form method="POST" action="/register" class="auth-form">
            @csrf

            <div class="role-toggle">
                <button type="button"
                        class="role-btn {{ old('role', 'CUSTOMER') === 'CUSTOMER' ? 'active' : '' }}"
                        data-role="CUSTOMER">
                    🏢 Customer
                </button>
                <button type="button"
                        class="role-btn {{ old('role') === 'ADMIN' ? 'active' : '' }}"
                        data-role="ADMIN">
                    🔧 Admin
                </button>
            </div>
            <input type="hidden" name="role" id="role-input" value="{{ old('role', 'CUSTOMER') }}">

            <div class="form-group">
                <label class="form-label" for="username">Username</label>
                <input type="text"
                       id="username"
                       name="username"
                       class="form-control"
                       placeholder="Enter your username"
                       value="{{ old('username') }}"
                       required>
                @error('username')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>

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
                <label class="form-label" for="phone">Phone Number</label>
                <input type="tel"
                       id="phone"
                       name="phone"
                       class="form-control"
                       placeholder="+1 (555) 000-0000"
                       value="{{ old('phone') }}"
                       required>
                @error('phone')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password">Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       class="form-control"
                       placeholder="Create a strong password"
                       required>
                @error('password')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirm Password</label>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="form-control"
                       placeholder="Repeat your password"
                       required>
                @error('password_confirmation')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>

            <div id="customer-fields" class="{{ old('role') === 'ADMIN' ? 'hidden' : '' }}">
                <div class="form-group">
                    <label class="form-label" for="company_name">Company Name</label>
                    <input type="text"
                           id="company_name"
                           name="company_name"
                           class="form-control"
                           placeholder="Your company name"
                           value="{{ old('company_name') }}">
                    @error('company_name')
                        <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="contact_person">Contact Person</label>
                    <input type="text"
                           id="contact_person"
                           name="contact_person"
                           class="form-control"
                           placeholder="Primary contact name"
                           value="{{ old('contact_person') }}">
                    @error('contact_person')
                        <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="address">Address</label>
                    <input type="text"
                           id="address"
                           name="address"
                           class="form-control"
                           placeholder="Street address, city"
                           value="{{ old('address') }}">
                    @error('address')
                        <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label class="form-label" for="country">Country</label>
                    <input type="text"
                           id="country"
                           name="country"
                           class="form-control"
                           placeholder="e.g. United States"
                           value="{{ old('country') }}">
                    @error('country')
                        <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <button type="submit" class="btn-auth" style="margin-top: 16px;">Create Account</button>
        </form>

        <div class="auth-footer">
            <p>Already have an account? <a href="/login" class="auth-link">Sign In</a></p>
        </div>

    </div>
</div>

@endsection
