@extends('layouts.app')

@section('title', 'Shipments Registry — Smart Shipping')

@section('content')

<div class="dashboard-wrapper">

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 24px;">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 24px;">{{ session('error') }}</div>
    @endif

    <!-- Filtering panel -->
    <div class="dashboard-table-card" style="padding: 24px; margin-bottom: 32px;">
        <h3 class="dashboard-table-title" style="font-size: 1rem; margin-bottom: 16px;">🔍 Filter & Search Shipments</h3>
        <form method="GET" action="/operator/shipments" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; align-items: end;">
            
            <div>
                <label class="form-label" style="font-size: 0.725rem;">Search Query</label>
                <input type="text" name="search" placeholder="Ref code or company name..." value="{{ request('search') }}" class="form-input">
            </div>

            <div>
                <label class="form-label" style="font-size: 0.725rem;">Shipment Status</label>
                <select name="status" class="form-input" style="background-color: var(--bg-primary);">
                    <option value="">All Statuses</option>
                    <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>PENDING</option>
                    <option value="BOOKED" {{ request('status') === 'BOOKED' ? 'selected' : '' }}>BOOKED</option>
                    <option value="IN_TRANSIT" {{ request('status') === 'IN_TRANSIT' ? 'selected' : '' }}>IN_TRANSIT</option>
                    <option value="AT_PORT" {{ request('status') === 'AT_PORT' ? 'selected' : '' }}>AT_PORT</option>
                    <option value="DELIVERED" {{ request('status') === 'DELIVERED' ? 'selected' : '' }}>DELIVERED</option>
                    <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>CANCELLED</option>
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 0.725rem;">Source Port</label>
                <select name="source_port_id" class="form-input" style="background-color: var(--bg-primary);">
                    <option value="">All Ports</option>
                    @foreach($ports as $port)
                        <option value="{{ $port->port_id }}" {{ request('source_port_id') == $port->port_id ? 'selected' : '' }}>
                            {{ $port->port_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 0.725rem;">Destination Port</label>
                <select name="destination_port_id" class="form-input" style="background-color: var(--bg-primary);">
                    <option value="">All Ports</option>
                    @foreach($ports as $port)
                        <option value="{{ $port->port_id }}" {{ request('destination_port_id') == $port->port_id ? 'selected' : '' }}>
                            {{ $port->port_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 0.725rem;">Start Date</label>
                <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-input">
            </div>

            <div>
                <label class="form-label" style="font-size: 0.725rem;">End Date</label>
                <input type="date" name="end_date" value="{{ request('end_date') }}" class="form-input">
            </div>

            <div style="grid-column: span 1; display: flex; gap: 8px;">
                <a href="/operator/shipments" class="btn-secondary" style="flex: 1; padding: 10px; font-size: 0.85rem;">Reset</a>
                <button type="submit" class="btn-primary" style="flex: 2; padding: 10px; font-size: 0.85rem;">Filter</button>
            </div>

        </form>
    </div>

    <!-- Registry table -->
    <div class="dashboard-table-card">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 20px;">
            <h2 class="dashboard-table-title" style="margin-bottom: 0;">🚢 Cargo Shipments Registry</h2>
            
            @if(Auth::user()->role === 'OPERATOR' || Auth::user()->role === 'CUSTOMER')
                <a href="/operator/shipments/create" class="btn-primary" style="width: auto; padding: 10px 20px; font-size: 0.85rem;">
                    ➕ New Booking
                </a>
            @endif
        </div>

        <div class="table-responsive">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Ref Code</th>
                        <th>Customer Company</th>
                        <th>Source Port</th>
                        <th>Destination Port</th>
                        <th>Shipment Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($shipments as $shipment)
                        <tr data-href="/operator/shipments/{{ $shipment->shipment_id }}">
                            <td style="font-weight: 700;">
                                <a href="/operator/shipments/{{ $shipment->shipment_id }}" style="color: var(--border-focus); text-decoration: none;">
                                    {{ $shipment->shipment_ref }}
                                </a>
                            </td>
                            <td style="font-weight: 600;">{{ $shipment->company_name }}</td>
                            <td>{{ $shipment->source_port }}</td>
                            <td>{{ $shipment->destination_port }}</td>
                            <td>
                                {{ $shipment->shipment_date ? \Carbon\Carbon::parse($shipment->shipment_date)->format('M d, Y') : 'N/A' }}
                            </td>
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
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                                No cargo shipment records found matching the query.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
