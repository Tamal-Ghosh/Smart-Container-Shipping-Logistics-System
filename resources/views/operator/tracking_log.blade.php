@extends('layouts.app')

@section('title', 'Log Tracking Event — Smart Shipping')

@section('content')

<div class="auth-wrapper" style="min-height: auto; padding: 60px 20px;">
    <div class="auth-card" style="max-width: 600px; padding: 40px;">

        <div class="auth-brand" style="margin-bottom: 24px; text-align: left;">
            <h1 style="font-size: 1.5rem; text-align: left;">Log Tracking Event</h1>
            <p style="text-align: left;">Submit a new real-time status update or checkpoint log for an active shipment.</p>
        </div>

        @if(session('success'))
            <div class="alert alert-success" style="margin-bottom: 20px;">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger" style="margin-bottom: 20px;">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger" style="margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 16px;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="/tracking/log" class="auth-form">
            @csrf

            <!-- Shipment Selection -->
            <div class="form-group">
                <label class="form-label" for="shipment_id">Select Cargo Shipment</label>
                <select id="shipment_id" name="shipment_id" class="form-control" style="background-color: var(--bg-input); color: var(--text-primary); cursor: pointer;" required>
                    <option value="" disabled selected>-- Choose Shipment Reference --</option>
                    @foreach($shipments as $shipment)
                        <option value="{{ $shipment->shipment_id }}" data-status="{{ $shipment->status }}" {{ old('shipment_id') == $shipment->shipment_id ? 'selected' : '' }}>
                            {{ $shipment->shipment_ref }} — {{ $shipment->customer->company_name ?? 'N/A' }} ({{ $shipment->sourcePort->port_code ?? 'N/A' }} ➔ {{ $shipment->destinationPort->port_code ?? 'N/A' }}) [{{ $shipment->status }}]
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Event Type -->
            <div class="form-group" style="margin-bottom: 20px;">
                <label class="form-label" for="event_type">Event Type</label>
                <select id="event_type" name="event_type" class="form-control" style="background-color: var(--bg-input); color: var(--text-primary); cursor: pointer;" required>
                    <option value="IN_TRANSIT" {{ old('event_type') == 'IN_TRANSIT' ? 'selected' : '' }}>IN_TRANSIT</option>
                    <option value="AT_PORT" {{ old('event_type') == 'AT_PORT' ? 'selected' : '' }}>AT_PORT</option>
                    <option value="DELIVERED" {{ old('event_type') == 'DELIVERED' ? 'selected' : '' }}>DELIVERED</option>
                    <option value="CANCELLED" id="cancel_option" style="display:none;" {{ old('event_type') == 'CANCELLED' ? 'selected' : '' }}>CANCELLED — (Refund will be issued)</option>
                </select>
            </div>
            <input type="hidden" name="status" value="ON_TIME">

            <!-- Current Location & Associated Port -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 16px; margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="location_select">Current Location / Port</label>
                    <select id="location_select" class="form-control" style="background-color: var(--bg-input); color: var(--text-primary); cursor: pointer;" required>
                        <option value="" disabled selected>-- Select Location / Port --</option>
                        @foreach($ports as $port)
                            <option value="{{ $port->port_id }}" data-name="{{ $port->port_name }}" {{ old('port_id') == $port->port_id ? 'selected' : '' }}>
                                {{ $port->port_name }} ({{ $port->port_code }})
                            </option>
                        @endforeach
                        <option value="OTHER">Other Location / Open Sea</option>
                    </select>
                </div>

                <div class="form-group" id="custom_location_container" style="margin-bottom: 0; display: none;">
                    <label class="form-label" for="custom_location">Custom Location Details</label>
                    <input type="text" id="custom_location" placeholder="e.g. Singapore Strait" class="form-control">
                </div>
            </div>

            <!-- Hidden fields to submit to controller -->
            <input type="hidden" id="location" name="location">
            <input type="hidden" id="port_id" name="port_id">

            <!-- Remarks -->
            <div class="form-group">
                <label class="form-label" for="remarks">Remarks / Details</label>
                <input type="text" id="remarks" name="remarks" placeholder="e.g. Vessel cleared customs checkpoint" class="form-control" value="{{ old('remarks') }}">
            </div>

            <!-- Submit Button -->
            <div style="display: flex; gap: 16px; margin-top: 28px;">
                <a href="/operator/dashboard" class="btn-secondary" style="flex: 1;">Cancel</a>
                <button type="submit" class="btn-primary" style="flex: 2;">Log Event & Update Status</button>
            </div>
        </form>

    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const eventTypeSelect = document.getElementById('event_type');
        const locationSelect = document.getElementById('location_select');
        const customLocationContainer = document.getElementById('custom_location_container');
        const customLocationInput = document.getElementById('custom_location');
        const hiddenLocation = document.getElementById('location');
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

        // Show/hide CANCELLED option based on selected shipment status
        const shipmentSelect = document.getElementById('shipment_id');
        const cancelOption = document.getElementById('cancel_option');

        function updateCancelOption() {
            if (!shipmentSelect || !cancelOption) return;
            const selectedOption = shipmentSelect.options[shipmentSelect.selectedIndex];
            const status = selectedOption ? selectedOption.getAttribute('data-status') : null;
            const canCancel = status === 'PENDING' || status === 'BOOKED';
            cancelOption.style.display = canCancel ? '' : 'none';
            // If CANCELLED was selected but no longer valid, reset to first option
            if (!canCancel && eventTypeSelect && eventTypeSelect.value === 'CANCELLED') {
                eventTypeSelect.value = 'IN_TRANSIT';
                handleEventTypeChange();
            }
        }

        if (shipmentSelect) {
            shipmentSelect.addEventListener('change', updateCancelOption);
            updateCancelOption(); // Initialize on load
        }
    });
</script>
@endsection

@endsection
