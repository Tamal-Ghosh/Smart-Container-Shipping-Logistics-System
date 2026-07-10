@extends('layouts.app')

@section('title', 'Global Search Results — Smart Shipping')

@section('content')

<div class="dashboard-wrapper">

    <div style="margin-bottom: 32px;">
        <span style="font-size: 0.725rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; display: block;">Search Results</span>
        <h1 style="font-family: 'Outfit', sans-serif; font-size: 1.8rem; color: var(--border-focus); margin-top: 4px;">Grouped results for "{{ $q ?? '' }}"</h1>
    </div>

    <!-- Match category: Shipments -->
    <div class="dashboard-table-card" style="margin-bottom: 32px;">
        <h2 class="dashboard-table-title">🚢 Matching Shipments ({{ count($shipments) }})</h2>
        @if(count($shipments) > 0)
            <div class="table-responsive">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Ref Code</th>
                            <th>Customer Company</th>
                            <th>Source Port</th>
                            <th>Destination Port</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shipments as $shipment)
                            <tr>
                                <td style="font-weight: 700; color: var(--border-focus);">{{ $shipment->shipment_ref }}</td>
                                <td>{{ $shipment->company_name }}</td>
                                <td>{{ $shipment->source_port }}</td>
                                <td>{{ $shipment->destination_port }}</td>
                                <td>
                                    <span class="badge badge-{{ strtolower($shipment->status) }}">
                                        {{ $shipment->status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="/operator/shipments/{{ $shipment->shipment_id }}" class="btn-secondary" style="width: auto; padding: 6px 14px; font-size: 0.8rem; border-radius: var(--radius-xs);">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="padding: 24px; color: var(--text-muted); font-size: 0.9rem; font-style: italic; text-align: center; margin: 0;">No matching shipment records found.</p>
        @endif
    </div>

    <!-- Match category: Containers -->
    <div class="dashboard-table-card" style="margin-bottom: 32px;">
        <h2 class="dashboard-table-title">📦 Matching Containers ({{ count($containers) }})</h2>
        @if(count($containers) > 0)
            <div class="table-responsive">
                <table class="dashboard-table">
                    <thead>
                        <tr>
                            <th>Container Number</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($containers as $container)
                            <tr>
                                <td style="font-weight: 700;">{{ $container->container_number }}</td>
                                <td>{{ $container->container_type }}</td>
                                <td>
                                    <span class="badge badge-{{ strtolower($container->status) }}">
                                        {{ $container->status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="/containers/{{ $container->container_id }}" class="btn-secondary" style="width: auto; padding: 6px 14px; font-size: 0.8rem; border-radius: var(--radius-xs);">
                                        Inspect
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p style="padding: 24px; color: var(--text-muted); font-size: 0.9rem; font-style: italic; text-align: center; margin: 0;">No matching container records found.</p>
        @endif
    </div>

    <!-- Match category: Customers -->
    @if(Auth::user()->role === 'ADMIN' || Auth::user()->role === 'OPERATOR')
        <div class="dashboard-table-card">
            <h2 class="dashboard-table-title">👥 Matching Customers ({{ count($customers) }})</h2>
            @if(count($customers) > 0)
                <div class="table-responsive">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Company Name</th>
                                <th>Contact Person</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Country</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($customers as $customer)
                                <tr>
                                    <td style="font-weight: 700;">{{ $customer->company_name }}</td>
                                    <td>{{ $customer->contact_person }}</td>
                                    <td>{{ $customer->email }}</td>
                                    <td>{{ $customer->phone }}</td>
                                    <td>{{ $customer->country }}</td>
                                    <td>
                                        <a href="/operator/shipments?search={{ urlencode($customer->company_name) }}" class="btn-secondary" style="width: auto; padding: 6px 14px; font-size: 0.8rem; border-radius: var(--radius-xs);">
                                            View Shipments
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p style="padding: 24px; color: var(--text-muted); font-size: 0.9rem; font-style: italic; text-align: center; margin: 0;">No matching customer records found.</p>
            @endif
        </div>
    @endif

</div>

@endsection
