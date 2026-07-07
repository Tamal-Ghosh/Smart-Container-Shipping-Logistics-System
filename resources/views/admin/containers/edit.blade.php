@extends('layouts.app')

@section('title', 'Edit Container — Smart Shipping')

@section('content')

<div class="auth-wrapper" style="min-height: auto; padding: 60px 20px;">
    <div class="auth-card" style="max-width: 600px; padding: 40px;">

        <div class="auth-brand" style="margin-bottom: 24px; text-align: left;">
            <h1 style="font-size: 1.5rem; text-align: left;">Edit Container Specifications</h1>
            <p style="text-align: left;">Modify container {{ $container->container_number }}.</p>
        </div>

        <form method="POST" action="/containers/{{ $container->container_id }}" class="auth-form">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label class="form-label" for="container_number">Container ISO Number (Unique)</label>
                <input type="text"
                       id="container_number"
                       name="container_number"
                       class="form-control"
                       placeholder="e.g. CSXU-990112"
                       value="{{ old('container_number', $container->container_number) }}"
                       required>
                @error('container_number')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label class="form-label" for="container_type">Container Type</label>
                <select id="container_type" name="container_type" class="form-control" style="background-color: var(--bg-input); color: var(--text-primary); cursor: pointer;" required>
                    <option value="" disabled>-- Select Container Type --</option>
                    <option value="NORMAL" {{ old('container_type', $container->container_type) === 'NORMAL' ? 'selected' : '' }}>NORMAL</option>
                    <option value="FREEZE" {{ old('container_type', $container->container_type) === 'FREEZE' ? 'selected' : '' }}>FREEZE</option>
                    <option value="CHEMICAL" {{ old('container_type', $container->container_type) === 'CHEMICAL' ? 'selected' : '' }}>CHEMICAL</option>
                </select>
                @error('container_type')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <div style="display: flex; gap: 16px; margin-top: 24px;">
                <a href="/containers" class="btn-secondary" style="flex: 1;">Cancel</a>
                <button type="submit" class="btn-primary" style="flex: 2;">Update Container</button>
            </div>
        </form>

    </div>
</div>

@endsection
