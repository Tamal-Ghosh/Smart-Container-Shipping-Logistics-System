@extends('layouts.app')

@section('title', 'Container Utilisation History — Smart Shipping')

@section('content')

<div class="dashboard-wrapper">

    <div style="margin-bottom: 24px;">
        <a href="/containers" class="btn-secondary" style="width: auto; display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px;">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Container Registry
        </a>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 2fr; gap: 32px; align-items: start; flex-wrap: wrap;">
        
        <!-- Left Side: Container Specifications Card -->
        <div class="dashboard-table-card" style="padding: 28px;">
            <h2 class="dashboard-table-title">📦 Specifications</h2>
            
            <div style="margin-top: 20px; display: flex; flex-direction: column; gap: 16px;">
                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; display: block;">ISO Number</span>
                    <strong style="font-size: 1.5rem; color: var(--border-focus); font-family: 'Outfit', sans-serif;">{{ $container->container_number }}</strong>
                </div>

                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; display: block;">Container Type</span>
                    <span style="font-size: 1.05rem; font-weight: 600;">{{ $container->container_type }}</span>
                </div>

                <div>
                    <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; display: block;">Current Status</span>
                    <div style="margin-top: 6px;">
                        @if($container->status === 'AVAILABLE')
                            <span class="badge" style="background-color: rgba(16, 185, 129, 0.1); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2);">
                                AVAILABLE
                            </span>
                        @elseif($container->status === 'ASSIGNED' || $container->status === 'IN_USE')
                            <span class="badge" style="background-color: rgba(245, 158, 11, 0.1); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.2);">
                                {{ $container->status === 'ASSIGNED' ? 'ASSIGNED' : 'IN USE' }}
                            </span>
                        @elseif($container->status === 'MAINTENANCE')
                            <span class="badge" style="background-color: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2);">
                                MAINTENANCE
                            </span>
                        @elseif($container->status === 'RETIRED')
                            <span class="badge" style="background-color: rgba(107, 114, 128, 0.1); color: #9ca3af; border: 1px solid rgba(107, 114, 128, 0.2);">
                                RETIRED
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Side: Utilisation History list from V_CONTAINER_UTILISATION -->
        <div class="dashboard-table-card" style="padding: 28px;">
            <h2 class="dashboard-table-title">⌛ Utilisation & Assignment History (PL/SQL)</h2>
            
            <div class="table-responsive" style="margin-top: 16px;">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Shipment Ref</th>
                            <th>Customer</th>
                            <th>Date Assigned</th>

                            <th>Weight (kg)</th>
                            <th>Shipment Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $record)
                            @if($record->shipment_id)
                                <tr>
                                    <td>
                                        <a href="/operator/shipments/{{ $record->shipment_id }}" style="font-weight: 700; color: var(--border-focus); text-decoration: none;">
                                            {{ $record->shipment_ref }}
                                        </a>
                                    </td>
                                    <td style="font-weight: 600;">{{ $record->customer_name }}</td>
                                    <td style="font-size: 0.85rem;">
                                        {{ \Carbon\Carbon::parse($record->assigned_at)->format('M d, Y H:i') }}
                                    </td>

                                    <td>{{ number_format($record->loaded_weight_kg) }} kg</td>
                                    <td>
                                        <span class="badge badge-{{ strtolower($record->shipment_status) }}">
                                            {{ $record->shipment_status }}
                                        </span>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                                    This container unit has never been assigned to any shipments yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

@endsection
