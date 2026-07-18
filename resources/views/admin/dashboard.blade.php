@extends('layouts.app')

@section('title', 'Admin Dashboard — Smart Shipping')

@section('content')

<div class="dashboard-wrapper">

    <div class="stats-grid">
        <div class="stat-card">
            <span class="stat-card-title">Total Shipments</span>
            <span class="stat-card-value">{{ number_format($stats->total_shipments ?? 0) }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-card-title">Active (In Transit)</span>
            <span class="stat-card-value">{{ number_format($stats->active_shipments ?? 0) }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-card-title">Delivered</span>
            <span class="stat-card-value">{{ number_format($stats->delivered_shipments ?? 0) }}</span>
        </div>
        <div class="stat-card" style="border-left: 3px solid #ef4444;">
            <span class="stat-card-title">Cancelled</span>
            <span class="stat-card-value" style="color: #f87171;">{{ number_format($stats->cancelled_shipments ?? 0) }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-card-title">Available Containers</span>
            <span class="stat-card-value">{{ number_format($stats->available_containers ?? 0) }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-card-title">Available Vessels/Ships</span>
            <span class="stat-card-value">{{ number_format($stats->available_vehicles ?? 0) }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-card-title">Total Customers</span>
            <span class="stat-card-value">{{ number_format($stats->total_customers ?? 0) }}</span>
        </div>
        <div class="stat-card" style="border-left: 3px solid var(--accent-green);">
            <span class="stat-card-title">Total Revenue</span>
            <span class="stat-card-value" style="color: var(--accent-green);">৳{{ number_format($stats->total_revenue ?? 0, 2) }}</span>
        </div>
        <div class="stat-card" style="border-left: 3px solid #f59e0b;">
            <span class="stat-card-title">Refunded Amount</span>
            <span class="stat-card-value" style="color: #fbbf24;">৳{{ number_format($stats->refunded_amount ?? 0, 2) }}</span>
        </div>
    </div>

    <div class="dashboard-table-card" style="margin-top: 32px;">
        <h2 class="dashboard-table-title">Recent Shipping Operations</h2>
        
        <div class="table-responsive">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Ref Code</th>
                        <th>Customer</th>
                        <th>Source Port</th>
                        <th>Destination Port</th>
                        <th>Date Booked</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentShipments as $shipment)
                        <tr data-href="/operator/shipments/{{ $shipment->shipment_id }}">
                            <td style="font-weight: 600;">
                                <a href="/operator/shipments/{{ $shipment->shipment_id }}" style="color: var(--border-focus); text-decoration: none; font-weight: 700;">
                                    {{ $shipment->shipment_ref }}
                                </a>
                            </td>
                            <td>{{ $shipment->company_name }}</td>
                            <td>{{ $shipment->source_port }}</td>
                            <td>{{ $shipment->destination_port }}</td>
                            <td>
                                {{ \Carbon\Carbon::parse($shipment->shipment_date)->format('M d, Y') }}
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
                            <td colspan="7" style="text-align: center; padding: 24px; color: var(--text-secondary);">
                                No recent shipment records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
