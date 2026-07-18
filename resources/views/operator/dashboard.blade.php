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
            <span class="stat-card-title">Available Vessels/Ships</span>
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
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingShipments as $shipment)
                            <tr data-href="/operator/shipments/{{ $shipment->shipment_id }}">
                                <td style="font-weight: 600;">
                                    <a href="/operator/shipments/{{ $shipment->shipment_id }}" style="color: var(--border-focus); text-decoration: none; font-weight: 700;">
                                        {{ $shipment->shipment_ref }}
                                    </a>
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
                                <td>
                                    <a href="/operator/shipments/{{ $shipment->shipment_id }}" class="btn-secondary" style="width: auto; padding: 6px 14px; font-size: 0.8rem; border-radius: var(--radius-xs); display: inline-block;">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 24px; color: var(--text-secondary);">
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



        </div>

    </div>

</div>

@endsection