@extends('layouts.app')

@section('title', 'Customer Dashboard — Smart Shipping')

@section('content')

<div class="dashboard-wrapper">

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-card-title">Total Shipments (PL/SQL)</span>
            <span class="stat-card-value">{{ number_format($shipmentCount ?? 0) }}</span>
        </div>
        <div class="stat-card" style="border-left: 3px solid var(--accent-green);">
            <span class="stat-card-title">Paid Amount</span>
            <span class="stat-card-value" style="color: var(--accent-green);">৳{{ number_format($payments['paid'] ?? 0, 2) }}</span>
        </div>
        <div class="stat-card" style="border-left: 3px solid #f59e0b;">
            <span class="stat-card-title">Pending Amount</span>
            <span class="stat-card-value" style="color: #f59e0b;">৳{{ number_format($payments['pending'] ?? 0, 2) }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-card-title">Total Invoiced</span>
            <span class="stat-card-value">৳{{ number_format($payments['total'] ?? 0, 2) }}</span>
        </div>
    </div>

    <div class="dashboard-grid">
        
        <div class="dashboard-table-card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 12px;">
                <h2 class="dashboard-table-title" style="margin-bottom: 0;">🚢 My Shipments</h2>
                <a href="/operator/shipments/create" class="btn-primary" style="width: auto; padding: 8px 16px; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; justify-content: center; height: auto;">
                    + Add Booking
                </a>
            </div>
            <div class="table-responsive">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Ref Code</th>
                            <th>Route</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shipments as $shipment)
                            <tr data-href="/operator/shipments/{{ $shipment->shipment_id }}">
                                <td style="font-weight: 600; color: var(--border-focus);">
                                    {{ $shipment->shipment_ref }}
                                </td>
                                <td>{{ $shipment->source_port }} → {{ $shipment->destination_port }}</td>
                                <td>
                                    <span class="badge badge-{{ strtolower($shipment->shipment_status) }}">
                                        {{ $shipment->shipment_status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 24px; color: var(--text-secondary);">
                                    You have no shipment records.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            <div class="dashboard-table-card" style="padding: 24px;">
                <h2 class="dashboard-table-title" style="margin-bottom: 16px; font-size: 1rem;">🔍 Quick Tracking</h2>
                <form method="GET" action="/tracking">
                    <div style="display: flex; flex-direction: column; gap: 12px;">
                        <input type="text" name="ref" placeholder="Enter Shipment Ref (e.g. SHP-001)" required class="form-input">
                        <button type="submit" class="btn-primary">
                            Track Shipment
                        </button>
                    </div>
                </form>
            </div>

            <div class="dashboard-table-card" style="padding: 24px;">
                <h2 class="dashboard-table-title" style="margin-bottom: 16px; font-size: 1rem;">📦 Latest Shipment Event</h2>
                @if($latestShipment && $latestTracking)
                    <div class="timeline-feed">
                        <div class="timeline-item">
                            <span style="font-size: 0.725rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                                {{ $latestShipment->shipment_ref }}
                            </span>
                            <h3 style="font-size: 1rem; font-weight: 700; color: var(--accent-primary); margin: 2px 0 6px 0;">
                                {{ $latestTracking->event_type }}
                            </h3>
                            <p style="font-size: 0.875rem; color: var(--text-primary); margin: 0 0 4px 0;">
                                📍 {{ $latestTracking->location ?? 'Unknown location' }}
                            </p>
                            @if($latestTracking->remarks)
                                <p style="font-size: 0.825rem; color: var(--text-secondary); font-style: italic; margin: 0 0 8px 0;">
                                    "{{ $latestTracking->remarks }}"
                                </p>
                            @endif
                            <span style="font-size: 0.75rem; color: var(--text-muted);">
                                {{ \Carbon\Carbon::parse($latestTracking->updated_at)->format('M d, Y h:i A') }}
                            </span>
                        </div>
                    </div>
                @else
                    <p style="font-size: 0.875rem; color: var(--text-secondary); text-align: center; padding: 12px 0;">
                        No recent events found.
                    </p>
                @endif
            </div>

        </div>

    </div>

</div>

@endsection
