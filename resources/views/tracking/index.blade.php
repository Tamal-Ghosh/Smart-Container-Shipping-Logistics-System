@extends('layouts.app')

@section('title', 'Track Shipment — Smart Shipping')

@section('content')

<div class="dashboard-wrapper">

    <div style="max-width: 600px; margin: 40px auto 0 auto;">
        
        @if(session('error'))
            <div class="alert alert-danger" style="margin-bottom: 24px; padding: 16px; border-radius: var(--radius-xs);">
                ⚠️ {{ session('error') }}
            </div>
        @endif

        <div class="dashboard-table-card" style="padding: 40px 32px; border-top: 4px solid var(--border-focus);">
            <div style="text-align: center; margin-bottom: 32px;">
                <span style="font-size: 2.5rem; display: block; margin-bottom: 16px;">🛰️</span>
                <h1 style="font-family: 'Outfit', sans-serif; font-size: 1.8rem; color: var(--text-primary); margin: 0 0 8px 0;">Cargo Tracking Center</h1>
                <p style="color: var(--text-muted); font-size: 0.95rem; margin: 0;">Enter your unique cargo shipment reference ID to view real-time tracking events and status logs.</p>
            </div>

            <form method="GET" action="/tracking">
                <div style="display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label class="form-label" style="font-size: 0.8rem; text-transform: uppercase; font-weight: 700; color: var(--text-muted);">Shipment Reference</label>
                        <input type="text" name="ref" placeholder="e.g. SHP-2026-00001" required value="{{ request('ref') }}" class="form-input" style="padding: 12px 16px; font-size: 1rem;">
                    </div>

                    <button type="submit" class="btn-primary" style="padding: 12px; font-size: 1rem; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 8px;">
                        🔍 Track Cargo Journey
                    </button>
                </div>
            </form>
        </div>

    </div>

</div>

@endsection
