@extends('layouts.app')

@section('title', 'Create Operator Account — Smart Shipping')

@section('content')

<div class="dashboard-wrapper">

    <div style="max-width: 600px; margin: 0 auto;">
        
        <div style="margin-bottom: 24px;">
            <a href="/admin/users" class="btn-secondary" style="width: auto; padding: 8px 16px; font-size: 0.85rem;">
                ⬅ Back to Registry
            </a>
        </div>

        <div class="dashboard-table-card" style="padding: 36px 32px;">
            <h2 class="dashboard-table-title" style="margin-bottom: 28px; font-size: 1.4rem;">➕ Create Operator Account</h2>

            @if($errors->any())
                <div class="alert alert-danger" style="margin-bottom: 24px; padding: 12px; border-radius: var(--radius-xs);">
                    <ul style="margin: 0; padding-left: 16px; font-size: 0.875rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/admin/users">
                @csrf

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" for="username">Username</label>
                    <input type="text" name="username" id="username" value="{{ old('username') }}" required class="form-input" placeholder="e.g. jsmith_operator">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" for="email">Email Address</label>
                    <input type="email" name="email" id="email" value="{{ old('email') }}" required class="form-input" placeholder="e.g. j.smith@shipping.com">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" for="password">Password</label>
                    <input type="password" name="password" id="password" required class="form-input" placeholder="Minimum 6 characters">
                </div>

                <div class="form-group" style="margin-bottom: 28px;">
                    <label class="form-label" for="password_confirmation">Confirm Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required class="form-input" placeholder="Re-type password">
                </div>

                <button type="submit" class="btn-primary" style="padding: 12px; font-size: 1rem; font-weight: 600;">
                    Create Operator Account
                </button>

            </form>
        </div>

    </div>

</div>

@endsection
