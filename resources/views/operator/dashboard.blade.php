@extends('layouts.app')

@section('title', 'Operator Dashboard — Smart Shipping')

@section('content')

<div class="dashboard-wrapper">

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-card-title">Available Containers</span>
            <span class="stat-card-value">{{ number_format($availableContainers ?? 0) }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-card-title">Available Vehicles</span>
            <span class="stat-card-value">{{ number_format($availableVehicles ?? 0) }}</span>
        </div>
        <div class="stat-card" style="border-left: 3px solid var(--accent-primary);">
            <span class="stat-card-title">Active Containers (PL/SQL)</span>
            <span class="stat-card-value" style="color: var(--accent-primary);">{{ number_format($activeContainers ?? 0) }}</span>
        </div>
        <div class="stat-card" style="border-left: 3px solid var(--accent-green);">
            <span class="stat-card-title">Today's Events Logged</span>
            <span class="stat-card-value" style="color: var(--accent-green);">{{ number_format(count($todayEvents ?? [])) }}</span>
        </div>
    </div>

    <div class="dashboard-grid">

        <div class="dashboard-table-card">
            <h2 class="dashboard-table-title">🚢 Actionable Shipments (Pending / Booked)</h2>
            <div class="table-responsive">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Ref Code</th>
                            <th>Customer</th>
                            <th>Route</th>
                            <th>Date Booked</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingShipments as $shipment)
                            <tr>
                                <td style="font-weight: 600; color: var(--border-focus);">
                                    {{ $shipment->shipment_ref }}
                                </td>
                                <td>{{ $shipment->company_name }}</td>
                                <td>{{ $shipment->source_port }} → {{ $shipment->destination_port }}</td>
                                <td>
                                    {{ $shipment->created_at ? \Carbon\Carbon::parse($shipment->created_at)->format('M d, Y') : 'N/A' }}
                                </td>
                                <td>
                                    <span class="badge badge-{{ strtolower($shipment->status) }}">
                                        {{ $shipment->status }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 24px; color: var(--text-secondary);">
                                    No pending or booked shipment records found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div style="display: flex; flex-direction: column; gap: 24px;">

            <div class="dashboard-table-card" style="padding: 24px;">
                <h2 class="dashboard-table-title" style="margin-bottom: 16px; font-size: 1rem;">⚡ Quick Actions</h2>
                <div style="display: flex; flex-direction: column; gap: 12px;">
                    <a href="/operator/shipments/create" class="btn-primary">
                        ➕ New Shipment
                    </a>
                    <a href="/operator/tracking/log" class="btn-secondary">
                        📝 Log Tracking Event
                    </a>
                </div>
            </div>

            <div class="dashboard-table-card" style="padding: 24px;">
                <h2 class="dashboard-table-title" style="margin-bottom: 16px; font-size: 1rem;">⏳ Today's Event Feed</h2>
                <div class="timeline-feed" style="max-height: 300px; overflow-y: auto;">
                    @forelse($todayEvents as $event)
                        <div class="timeline-item">
                            <span style="font-size: 0.725rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em;">
                                {{ $event->shipment_ref }}
                            </span>
                            <h4 style="font-size: 0.875rem; font-weight: 700; color: var(--accent-primary); margin: 2px 0 4px 0;">
                                {{ $event->event_type ?? $event->status }}
                            </h4>
                            <p style="font-size: 0.825rem; color: var(--text-primary); margin: 0 0 2px 0;">
                                📍 {{ $event->location }}
                            </p>
                            <span style="font-size: 0.75rem; color: var(--text-muted);">
                                {{ \Carbon\Carbon::parse($event->updated_at)->format('h:i A') }}
                            </span>
                        </div>
                    @empty
                        <p style="font-size: 0.875rem; color: var(--text-secondary); text-align: center; padding: 12px 0; margin: 0;">
                            No events logged today yet.
                        </p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>

@endsection