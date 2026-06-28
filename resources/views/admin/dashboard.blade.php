@extends('layouts.app')

@section('title', 'Admin Dashboard — Smart Shipping')

@section('content')

<div class="dashboard-wrapper">

    {{-- Header --}}
    <div class="dashboard-header" style="margin-bottom: 24px;">
        <div>
            <h1 style="font-size: 1.5rem; font-weight: 700; color: var(--accent-primary);">⚓ Admin Overview</h1>
            <p style="color: var(--text-secondary); font-size: 0.875rem; margin-top: 4px;">Live tracking, assets availability, and billing statistics.</p>
        </div>
    </div>

    {{-- Quick Navigation --}}
    <div class="quick-nav">
        <span class="quick-nav-title">Quick Navigation:</span>
        <div class="quick-nav-links">
            <a href="/ports" class="quick-nav-btn">🏢 Ports</a>
            <a href="/vehicles" class="quick-nav-btn">🚚 Vehicles</a>
            <a href="/containers" class="quick-nav-btn">📦 Containers</a>
            <a href="/admin/users" class="quick-nav-btn">👥 Users</a>
            <a href="/admin/reports" class="quick-nav-btn">📊 Reports</a>
        </div>
    </div>

    {{-- Stats Grid --}}
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
        <div class="stat-card">
            <span class="stat-card-title">Available Containers</span>
            <span class="stat-card-value">{{ number_format($stats->available_containers ?? 0) }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-card-title">Available Vehicles</span>
            <span class="stat-card-value">{{ number_format($stats->available_vehicles ?? 0) }}</span>
        </div>
        <div class="stat-card">
            <span class="stat-card-title">Total Customers</span>
            <span class="stat-card-value">{{ number_format($stats->total_customers ?? 0) }}</span>
        </div>
        <div class="stat-card" style="grid-column: span 1; border-left: 3px solid var(--accent-green);">
            <span class="stat-card-title">Total Revenue</span>
            <span class="stat-card-value" style="color: var(--accent-green);">
                ৳{{ number_format($stats->total_revenue ?? 0, 2) }}
            </span>
        </div>
    </div>

    {{-- Recent Shipments Table --}}
    <div class="dashboard-table-card">
        <h2 class="dashboard-table-title">🚢 Recent Shipping Operations</h2>
        
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
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentShipments as $shipment)
                        <tr>
                            <td style="font-weight: 600; color: var(--accent-primary);">
                                {{ $shipment->shipment_ref }}
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 24px; color: var(--text-secondary);">
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
