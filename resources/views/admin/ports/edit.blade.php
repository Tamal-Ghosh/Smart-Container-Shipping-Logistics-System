@extends('layouts.app')

@section('title', 'Edit Port — Smart Shipping')

@section('content')

<div class="auth-wrapper" style="min-height: auto; padding: 60px 20px;">
    <div class="auth-card" style="max-width: 600px; padding: 40px;">

        <div class="auth-brand" style="margin-bottom: 24px; text-align: left;">
            <h1 style="font-size: 1.5rem; text-align: left;">Edit Port Terminal</h1>
            <p style="text-align: left;">Modify the details for the registered port terminal.</p>
        </div>

        <form method="POST" action="/ports/{{ $port->port_id }}" class="auth-form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="port_name">Port Name</label>
                <input type="text"
                       id="port_name"
                       name="port_name"
                       class="form-control"
                       value="{{ old('port_name', $port->port_name) }}"
                       required>
                @error('port_name')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="port_code">Port Code (Unique)</label>
                <input type="text"
                       id="port_code"
                       name="port_code"
                       class="form-control"
                       value="{{ old('port_code', $port->port_code) }}"
                       required>
                @error('port_code')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="location">Location / City</label>
                <input type="text"
                       id="location"
                       name="location"
                       class="form-control"
                       value="{{ old('location', $port->location) }}">
                @error('location')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="country">Country</label>
                <input type="text"
                       id="country"
                       name="country"
                       class="form-control"
                       value="{{ old('country', $port->country) }}">
                @error('country')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 16px; margin-top: 24px;">
                <a href="/ports" class="btn-secondary" style="flex: 1;">Cancel</a>
                <button type="submit" class="btn-primary" style="flex: 2;">Update Port</button>
            </div>
        </form>

    </div>
</div>

@endsection
