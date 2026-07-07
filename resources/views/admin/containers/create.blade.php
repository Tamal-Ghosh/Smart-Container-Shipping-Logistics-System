@extends('layouts.app')

@section('title', 'Add Container — Smart Shipping')

@section('content')

<div class="auth-wrapper" style="min-height: auto; padding: 60px 20px;">
    <div class="auth-card" style="max-width: 600px; padding: 40px;">

        <div class="auth-brand" style="margin-bottom: 24px; text-align: left;">
            <h1 style="font-size: 1.5rem; text-align: left;">Register New Container</h1>
            <p style="text-align: left;">Fill in the details to register a new shipping container unit.</p>
        </div>

        <form method="POST" action="/containers" class="auth-form">
            @csrf

            <div class="form-group">
                <label class="form-label" for="container_number">Container ISO Number (Unique)</label>
                <input type="text"
                       id="container_number"
                       name="container_number"
                       class="form-control"
                       placeholder="e.g. CSXU-990112"
                       value="{{ old('container_number') }}"
                       required>
                @error('container_number')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="container_type">Container Type</label>
                <select id="container_type" name="container_type" class="form-control" style="background-color: var(--bg-input); color: var(--text-primary); cursor: pointer;" required>
                    <option value="" disabled selected>-- Select Container Type --</option>
                    <option value="NORMAL" {{ old('container_type') === 'NORMAL' ? 'selected' : '' }}>NORMAL</option>
                    <option value="FREEZE" {{ old('container_type') === 'FREEZE' ? 'selected' : '' }}>FREEZE</option>
                    <option value="CHEMICAL" {{ old('container_type') === 'CHEMICAL' ? 'selected' : '' }}>CHEMICAL</option>
                </select>
                @error('container_type')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 16px; margin-top: 24px;">
                <a href="/containers" class="btn-secondary" style="flex: 1;">Cancel</a>
                <button type="submit" class="btn-primary" style="flex: 2;">Save Container</button>
            </div>
        </form>

    </div>
</div>

@endsection
