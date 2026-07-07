@extends('layouts.app')

@section('title', 'Edit Vessel — Smart Shipping')

@section('content')

<div class="auth-wrapper" style="min-height: auto; padding: 60px 20px;">
    <div class="auth-card" style="max-width: 600px; padding: 40px;">

        <div class="auth-brand" style="margin-bottom: 24px; text-align: left;">
            <h1 style="font-size: 1.5rem; text-align: left;">Edit Vessel Details</h1>
            <p style="text-align: left;">Update the specifications for vessel {{ $vehicle->vehicle_number }}.</p>
        </div>

        <form method="POST" action="/vehicles/{{ $vehicle->vehicle_id }}" class="auth-form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="vehicle_number">Vessel Number (Unique)</label>
                <input type="text"
                       id="vehicle_number"
                       name="vehicle_number"
                       class="form-control"
                       placeholder="e.g. VSL-TITAN"
                       value="{{ old('vehicle_number', $vehicle->vehicle_number) }}"
                       required>
                @error('vehicle_number')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="type">Vessel Type</label>
                <select id="type" name="type" class="form-control" style="background-color: var(--bg-input); color: var(--text-primary); cursor: pointer;" required>
                    <option value="VESSEL" selected>VESSEL</option>
                </select>
                @error('type')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="capacity_kg">Capacity (kg)</label>
                <input type="number"
                       id="capacity_kg"
                       name="capacity_kg"
                       class="form-control"
                       placeholder="e.g. 50000"
                       value="{{ old('capacity_kg', $vehicle->capacity_kg) }}"
                       min="0"
                       required>
                @error('capacity_kg')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 16px; margin-top: 24px;">
                <a href="/vehicles" class="btn-secondary" style="flex: 1;">Cancel</a>
                <button type="submit" class="btn-primary" style="flex: 2;">Update Vessel</button>
            </div>
        </form>

    </div>
</div>

@endsection
