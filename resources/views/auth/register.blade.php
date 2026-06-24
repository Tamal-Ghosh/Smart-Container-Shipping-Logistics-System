@extends('layouts.app')

@section('title', 'Create Account — Smart Shipping')

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
            <p>Create your account</p>
        </div>

        {{-- Session Error --}}
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif

        {{-- Session Success --}}
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Registration Form --}}
        <form method="POST" action="/register" class="auth-form">
            @csrf

            {{-- Role Toggle --}}
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

            {{-- ── Common Fields ───────────────────────── --}}

            {{-- Username --}}
            <div class="form-group">
                <label class="form-label" for="username">Username <span class="required">*</span></label>
                <input type="text"
                       id="username"
                       name="username"
                       class="form-input"
                       placeholder="Enter your username"
                       value="{{ old('username') }}"
                       required>
                @error('username')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

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

            {{-- Phone --}}
            <div class="form-group">
                <label class="form-label" for="phone">Phone Number <span class="required">*</span></label>
                <input type="tel"
                       id="phone"
                       name="phone"
                       class="form-input"
                       placeholder="+1 (555) 000-0000"
                       value="{{ old('phone') }}"
                       required>
                @error('phone')
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
                       placeholder="Create a strong password"
                       required>
                @error('password')
                    <span class="error-text">{{ $message }}</span>
                @enderror

                {{-- Password Strength Hints --}}
                <div class="password-hints">
                    <div class="password-hint invalid" id="hint-length">
                        <span class="hint-icon">✗</span> At least 8 characters
                    </div>
                    <div class="password-hint invalid" id="hint-upper">
                        <span class="hint-icon">✗</span> Contains uppercase letter
                    </div>
                    <div class="password-hint invalid" id="hint-lower">
                        <span class="hint-icon">✗</span> Contains lowercase letter
                    </div>
                    <div class="password-hint invalid" id="hint-number">
                        <span class="hint-icon">✗</span> Contains a number
                    </div>
                    <div class="password-hint invalid" id="hint-special">
                        <span class="hint-icon">✗</span> Contains special character
                    </div>
                </div>
            </div>

            {{-- Confirm Password --}}
            <div class="form-group">
                <label class="form-label" for="password_confirmation">Confirm Password <span class="required">*</span></label>
                <input type="password"
                       id="password_confirmation"
                       name="password_confirmation"
                       class="form-input"
                       placeholder="Repeat your password"
                       required>
                <div class="match-indicator" id="match-indicator"></div>
                @error('password_confirmation')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            {{-- ── Customer-Only Fields ────────────────── --}}
            <div id="customer-fields" class="{{ old('role') === 'ADMIN' ? 'hidden' : '' }}">

                <div class="section-divider">
                    <span>Company Details</span>
                </div>

                {{-- Company Name --}}
                <div class="form-group">
                    <label class="form-label" for="company_name">Company Name <span class="required">*</span></label>
                    <input type="text"
                           id="company_name"
                           name="company_name"
                           class="form-input"
                           placeholder="Your company name"
                           value="{{ old('company_name') }}">
                    @error('company_name')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Contact Person --}}
                <div class="form-group">
                    <label class="form-label" for="contact_person">Contact Person <span class="required">*</span></label>
                    <input type="text"
                           id="contact_person"
                           name="contact_person"
                           class="form-input"
                           placeholder="Primary contact name"
                           value="{{ old('contact_person') }}">
                    @error('contact_person')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Address --}}
                <div class="form-group">
                    <label class="form-label" for="address">Address <span class="required">*</span></label>
                    <input type="text"
                           id="address"
                           name="address"
                           class="form-input"
                           placeholder="Street address, city"
                           value="{{ old('address') }}">
                    @error('address')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Country --}}
                <div class="form-group">
                    <label class="form-label" for="country">Country <span class="required">*</span></label>
                    <input type="text"
                           id="country"
                           name="country"
                           class="form-input"
                           placeholder="e.g. United States"
                           value="{{ old('country') }}">
                    @error('country')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-submit">Create Account</button>
        </form>

        {{-- Footer Link --}}
        <div class="auth-footer">
            <p>Already have an account? <a href="/login" class="auth-link">Sign In</a></p>
        </div>

    </div>
</div>

@endsection
