@extends('layouts.app')

@section('title', 'Shipment Detail — Smart Shipping')

@section('content')

<div class="dashboard-wrapper">

    <!-- Header navigation -->
    <div style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <a href="{{ Auth::user()->role === 'CUSTOMER' ? '/customer/dashboard' : '/operator/shipments' }}" class="btn-secondary" style="width: auto; display: inline-flex; align-items: center; gap: 8px; padding: 10px 20px;">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
            Back to Dashboard
        </a>


    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 24px;">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 24px;">{{ session('error') }}</div>
    @endif

    <!-- Main Grid layout -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; align-items: start; flex-wrap: wrap;">
        
        <!-- Left Column: Specs, Admin controls & Event Logger -->
        <div style="display: flex; flex-direction: column; gap: 32px;">
            
            <!-- Specs Card -->
            <div class="dashboard-table-card" style="padding: 28px;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 1px solid var(--border-subtle); padding-bottom: 16px; margin-bottom: 20px;">
                    <div>
                        <span style="font-size: 0.725rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; display: block;">Reference Code</span>
                        <h2 style="font-family: 'Outfit', sans-serif; font-size: 1.6rem; color: var(--border-focus); margin-top: 4px;">{{ $shipment->shipment_ref }}</h2>
                    </div>
                    <div>
                        <span style="font-size: 0.725rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; display: block; text-align: right; margin-bottom: 6px;">Shipment Status</span>
                        <span class="badge badge-{{ strtolower($shipment->status) }}">
                            {{ $shipment->status }}
                        </span>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <span style="font-size: 0.725rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Customer</span>
                        <p style="font-weight: 600; margin-top: 4px;">{{ $shipment->customer->company_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span style="font-size: 0.725rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Carrier Vessel/Ship</span>
                        <p style="font-weight: 600; margin-top: 4px;">{{ $shipment->vehicle->vehicle_number ?? 'N/A' }} ({{ $shipment->vehicle->type ?? 'N/A' }})</p>
                    </div>

                    <div>
                        <span style="font-size: 0.725rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Source Port</span>
                        <p style="font-weight: 600; margin-top: 4px;">{{ $shipment->sourcePort->port_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <span style="font-size: 0.725rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Destination Port</span>
                        <p style="font-weight: 600; margin-top: 4px;">{{ $shipment->destinationPort->port_name ?? 'N/A' }}</p>
                    </div>

                    <div>
                        <span style="font-size: 0.725rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Date Booked</span>
                        <p style="font-weight: 600; margin-top: 4px;">{{ $shipment->shipment_date ? \Carbon\Carbon::parse($shipment->shipment_date)->format('M d, Y') : 'N/A' }}</p>
                    </div>
                </div>

                <div style="border-top: 1px solid var(--border-subtle); padding-top: 16px; margin-bottom: 20px;">
                    <span style="font-size: 0.725rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; display: block;">Notes</span>
                    <p style="margin-top: 6px; font-size: 0.9rem; color: var(--text-secondary);">{{ $shipment->notes ?? 'No special instructions registered.' }}</p>
                </div>

                <!-- Payment Block -->
                <div style="border-top: 1px solid var(--border-subtle); padding-top: 16px; display: flex; justify-content: space-between; align-items: center;">
                    <div>
                        <span style="font-size: 0.725rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; display: block;">Billing Amount</span>
                        <strong style="font-size: 1.2rem; font-family: 'Outfit', sans-serif;">{{ $payment ? '$' . number_format($payment->amount, 2) : 'N/A' }}</strong>
                    </div>
                    <div>
                        <span style="font-size: 0.725rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; display: block; text-align: right; margin-bottom: 4px;">Payment Status</span>
                        @if($payment)
                            <span class="badge badge-{{ strtolower($payment->payment_status) }}">
                                {{ $payment->payment_status }}
                            </span>
                        @else
                            <span class="badge" style="background-color: rgba(107, 114, 128, 0.1); color: #9ca3af; border: 1px solid rgba(107, 114, 128, 0.2);">UNBILLED</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Cancel Shipment (PENDING/BOOKED only) -->
            @if(in_array(Auth::user()->role, ['OPERATOR','ADMIN']) && in_array($shipment->status, ['PENDING','BOOKED']))
                <div class="dashboard-table-card" style="padding: 20px; border-left: 3px solid #ef4444;">
                    <h3 class="dashboard-table-title" style="font-size: 0.95rem; margin-bottom: 12px; color: #f87171;">❌ Cancel Shipment</h3>
                    <p style="font-size: 0.85rem; color: var(--text-secondary); margin-bottom: 16px;">Cancelling will immediately set shipment status to <strong>CANCELLED</strong> and payment status to <strong>REFUNDED</strong>. This cannot be undone.</p>
                    <form method="POST" action="/operator/shipments/{{ $shipment->shipment_id }}/cancel" onsubmit="return confirm('Are you sure you want to cancel this shipment? Payment will be refunded.')">
                        @csrf
                        <button type="submit" style="width: 100%; padding: 12px; background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.5); color: #f87171; border-radius: var(--radius-xs); font-weight: 700; cursor: pointer; font-size: 0.9rem; transition: background 0.2s;" onmouseover="this.style.background='rgba(239,68,68,0.3)'" onmouseout="this.style.background='rgba(239,68,68,0.15)'">
                            Cancel Shipment &amp; Refund Payment
                        </button>
                    </form>
                </div>
            @endif

            <!-- Operator Log event form -->
            @if((Auth::user()->role === 'OPERATOR' || Auth::user()->role === 'ADMIN') && $shipment->status !== 'CANCELLED' && $shipment->status !== 'DELIVERED')
                <div class="dashboard-table-card" style="padding: 24px;">
                    <h3 class="dashboard-table-title" style="font-size: 1rem; margin-bottom: 16px;">📝 Update Status &amp; Log Tracking Event</h3>
                    <form method="POST" action="/tracking/log" class="auth-form" style="display: flex; flex-direction: column; gap: 16px;">
                        @csrf
                        <input type="hidden" name="shipment_id" value="{{ $shipment->shipment_id }}">
                        
                        <div class="form-group">
                            <label class="form-label" style="font-size: 0.7rem;">Event Type</label>
                            <select id="event_type" name="event_type" class="form-input" style="background-color: var(--bg-primary);" required>
                                <option value="BOOKED">BOOKED</option>
                                <option value="IN_TRANSIT">IN_TRANSIT</option>
                                <option value="AT_PORT">AT_PORT</option>
                                <option value="DELIVERED">DELIVERED</option>
                            </select>
                        </div>
                        <input type="hidden" name="status" value="ON_TIME">

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px;">
                            <div>
                                <label class="form-label" style="font-size: 0.7rem;">Current Location / Port</label>
                                <select id="location_select" class="form-input" style="background-color: var(--bg-primary);" required>
                                    <option value="" disabled selected>-- Select Location / Port --</option>
                                    @foreach($ports as $port)
                                        <option value="{{ $port->port_id }}" data-name="{{ $port->port_name }}">{{ $port->port_name }} ({{ $port->port_code }})</option>
                                    @endforeach
                                    <option value="OTHER">Other Location / Open Sea</option>
                                </select>
                            </div>
                            <div id="custom_location_container" style="display: none;">
                                <label class="form-label" style="font-size: 0.7rem;">Custom Location Details</label>
                                <input type="text" id="custom_location" placeholder="e.g. Terminal 2, Singapore Port" class="form-input">
                            </div>
                        </div>

                        <!-- Hidden fields to submit to controller -->
                        <input type="hidden" id="event_location" name="location">
                        <input type="hidden" id="port_id" name="port_id">

                        <div>
                            <label class="form-label" style="font-size: 0.7rem;">Remarks / Details</label>
                            <input type="text" name="remarks" placeholder="e.g. Vessel cleared customs checkpoint" class="form-input">
                        </div>

                        <button type="submit" class="btn-primary" style="padding: 12px; box-shadow: none;">Log New Event & Update Status</button>
                    </form>
                </div>
            @endif

        </div>

        <!-- Right Column: Assigned Containers & Event Timeline -->
        <div style="display: flex; flex-direction: column; gap: 32px;">
            
            <!-- Assigned Containers Card -->
            <div class="dashboard-table-card" style="padding: 28px;">
                <h2 class="dashboard-table-title">📦 Assigned Cargo Containers</h2>
                <div class="table-responsive" style="margin-top: 12px;">
                    <table class="dashboard-table">
                        <thead>
                            <tr>
                                <th>Container ISO</th>
                                <th>Type</th>
                                <th>Loaded Weight</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignments as $assignment)
                                <tr>
                                    <td style="font-weight: 700; color: var(--border-focus);">{{ $assignment->container_number }}</td>
                                    <td style="font-weight: 600;">{{ $assignment->container_type }}</td>

                                    <td>{{ number_format($assignment->loaded_weight_kg) }} kg</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; padding: 24px; color: var(--text-secondary);">
                                        No containers assigned to this shipment.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Tracking Timeline Card -->
            <div class="dashboard-table-card" style="padding: 28px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h2 class="dashboard-table-title" style="margin-bottom: 0;">🛰️ Live Shipment Tracking Timeline</h2>
                    <a href="/tracking/{{ $shipment->shipment_ref }}" class="btn-secondary" style="width: auto; padding: 6px 14px; font-size: 0.8rem; border-radius: var(--radius-xs);">
                        Full Timeline
                    </a>
                </div>

                <div class="timeline-feed" style="margin-top: 20px;">
                    @forelse($timeline as $event)
                        <div class="timeline-item">
                            <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                                <h4 style="font-size: 0.95rem; font-weight: 700; color: var(--border-focus); margin: 0;">
                                    {{ $event->event_type }}
                                </h4>
                            </div>
                            
                            <p style="font-size: 0.875rem; color: var(--text-primary); margin: 4px 0 2px 0;">
                                📍 <strong>Location:</strong> {{ $event->location }} {{ $event->port_name ? '(' . $event->port_name . ')' : '' }}
                            </p>
                            @if($event->remarks)
                                <p style="font-size: 0.825rem; color: var(--text-secondary); margin: 2px 0 4px 0; font-style: italic;">
                                    "{{ $event->remarks }}"
                                </p>
                            @endif
                            <span style="font-size: 0.775rem; color: var(--text-muted);">
                                ⏰ {{ \Carbon\Carbon::parse($event->updated_at)->format('M d, Y — h:i A') }}
                            </span>
                        </div>
                    @empty
                        <p style="text-align: center; padding: 32px; color: var(--text-secondary); font-style: italic;">
                            No tracking events logged yet for this shipment.
                        </p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const eventTypeSelect = document.getElementById('event_type');
        const locationSelect = document.getElementById('location_select');
        const customLocationContainer = document.getElementById('custom_location_container');
        const customLocationInput = document.getElementById('custom_location');
        const hiddenLocation = document.getElementById('event_location');
        const hiddenPortId = document.getElementById('port_id');

        function handleLocationChange() {
            if (eventTypeSelect && eventTypeSelect.value === 'CANCELLED') {
                return;
            }
            if (locationSelect.value === 'OTHER') {
                customLocationContainer.style.display = 'block';
                customLocationInput.required = true;
                hiddenPortId.value = '';
                hiddenLocation.value = customLocationInput.value;
            } else {
                customLocationContainer.style.display = 'none';
                customLocationInput.required = false;
                customLocationInput.value = '';
                
                const selectedOption = locationSelect.options[locationSelect.selectedIndex];
                if (selectedOption && selectedOption.value) {
                    hiddenPortId.value = selectedOption.value; // Port ID
                    hiddenLocation.value = selectedOption.getAttribute('data-name'); // Port Name
                } else {
                    hiddenPortId.value = '';
                    hiddenLocation.value = '';
                }
            }
        }

        function handleEventTypeChange() {
            if (eventTypeSelect.value === 'CANCELLED') {
                if (locationSelect && locationSelect.parentElement) {
                    locationSelect.parentElement.style.display = 'none';
                }
                locationSelect.disabled = true;   // bypass HTML5 required validation
                locationSelect.required = false;
                customLocationContainer.style.display = 'none';
                customLocationInput.disabled = true;
                customLocationInput.required = false;

                hiddenPortId.value = '';
                hiddenLocation.value = 'CANCELLED';
            } else {
                if (locationSelect && locationSelect.parentElement) {
                    locationSelect.parentElement.style.display = 'block';
                }
                locationSelect.disabled = false;
                locationSelect.required = true;
                customLocationInput.disabled = false;
                handleLocationChange();
            }
        }

        if (locationSelect && customLocationContainer && customLocationInput && hiddenLocation && hiddenPortId) {
            locationSelect.addEventListener('change', handleLocationChange);
            customLocationInput.addEventListener('input', function() {
                if (locationSelect.value === 'OTHER') {
                    hiddenLocation.value = this.value;
                }
            });

            if (eventTypeSelect) {
                eventTypeSelect.addEventListener('change', handleEventTypeChange);
            }

            // Initialize state
            if (eventTypeSelect && eventTypeSelect.value === 'CANCELLED') {
                handleEventTypeChange();
            } else {
                handleLocationChange();
            }
        }
    });
</script>
@endsection

@endsection
