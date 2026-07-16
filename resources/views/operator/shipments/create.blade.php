@extends('layouts.app')

@section('title', 'New Shipment Booking — Smart Shipping')

@section('content')

<div class="auth-wrapper" style="min-height: auto; padding: 60px 20px;">
    <div class="auth-card" style="max-width: 800px; padding: 40px;">

        <div class="auth-brand" style="margin-bottom: 24px; text-align: left;">
            <h1 style="font-size: 1.5rem; text-align: left;">Book New Shipment</h1>
            <p style="text-align: left;">Book a cargo shipment and assign available containers and carrier fleet vessel.</p>
        </div>

        @if($errors->has('error'))
            <div class="alert alert-danger" style="margin-bottom: 20px;">{{ $errors->first('error') }}</div>
        @endif

        <form method="POST" action="/operator/shipments" class="auth-form">
            @csrf

            <!-- Customer Selection (Only for Operator, Auto-filled for Customer) -->
            @if(Auth::user()->role === 'OPERATOR')
                <div class="form-group">
                    <label class="form-label" for="customer_id">Customer Client</label>
                    <select id="customer_id" name="customer_id" class="form-control" style="background-color: var(--bg-input); color: var(--text-primary); cursor: pointer;" required>
                        <option value="" disabled selected>-- Select Customer Corporation --</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->customer_id }}" {{ old('customer_id') == $customer->customer_id ? 'selected' : '' }}>
                                {{ $customer->company_name }} ({{ $customer->contact_person }})
                            </option>
                        @endforeach
                    </select>
                    @error('customer_id')
                        <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>
            @else
                <div class="form-group">
                    <label class="form-label">Customer Client (Auto-filled)</label>
                    <input type="text" class="form-control" value="{{ $myCustomer->company_name ?? '' }}" readonly style="opacity: 0.7;">
                </div>
            @endif

            <!-- Ports Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="source_port_id">Source Port</label>
                    <select id="source_port_id" name="source_port_id" class="form-control" style="background-color: var(--bg-input); color: var(--text-primary); cursor: pointer;" required>
                        <option value="" disabled selected>-- Source Terminal --</option>
                        @foreach($ports as $port)
                            <option value="{{ $port->port_id }}" {{ old('source_port_id') == $port->port_id ? 'selected' : '' }}>
                                {{ $port->port_name }} ({{ $port->port_code }})
                            </option>
                        @endforeach
                    </select>
                    @error('source_port_id')
                        <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label" for="destination_port_id">Destination Port</label>
                    <select id="destination_port_id" name="destination_port_id" class="form-control" style="background-color: var(--bg-input); color: var(--text-primary); cursor: pointer;" required>
                        <option value="" disabled selected>-- Destination Terminal --</option>
                        @foreach($ports as $port)
                            <option value="{{ $port->port_id }}" {{ old('destination_port_id') == $port->port_id ? 'selected' : '' }}>
                                {{ $port->port_name }} ({{ $port->port_code }})
                            </option>
                        @endforeach
                    </select>
                    @error('destination_port_id')
                        <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem; display: block;">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Vessel Selection (AVAILABLE ONLY) -->
            <div class="form-group">
                <label class="form-label" for="vehicle_id">Assign Carrier Vessel/Ship</label>
                <select id="vehicle_id" name="vehicle_id" class="form-control" style="background-color: var(--bg-input); color: var(--text-primary); cursor: pointer;" required>
                    <option value="" disabled selected>-- Select Available Carrier Vessel --</option>
                    @foreach($vehicles as $vehicle)
                        <option value="{{ $vehicle->vehicle_id }}" {{ old('vehicle_id') == $vehicle->vehicle_id ? 'selected' : '' }}>
                            [{{ $vehicle->type }}] {{ $vehicle->vehicle_number }} — (Capacity: {{ number_format($vehicle->capacity_kg) }} kg)
                        </option>
                    @endforeach
                </select>
                <small style="color: var(--text-muted); font-size: 0.775rem; margin-top: 4px; display: block; font-style: italic;">
                    ⚠️ Note: Only fleet vessels/ships currently marked as <strong>AVAILABLE</strong> are listed.
                </small>
                @error('vehicle_id')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Multi-select Containers Section -->
            <div class="form-group">
                <label class="form-label">Select Cargo Containers (AVAILABLE ONLY)</label>
                <div style="background-color: var(--bg-input); padding: 16px; border-radius: var(--radius-sm); border: 1px solid var(--border-input); max-height: 280px; overflow-y: auto; display: flex; flex-direction: column; gap: 12px;">
                    @forelse($containers as $index => $container)
                        <div style="display: flex; flex-direction: column; gap: 8px; border-bottom: 1px solid rgba(255,255,255,0.03); padding-bottom: 12px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-weight: 600;">
                                <input type="checkbox" 
                                       name="containers[]" 
                                       value="{{ $container->container_id }}" 
                                       class="container-checkbox" 
                                       data-target="assignment-details-{{ $container->container_id }}"
                                       style="width: 18px; height: 18px; accent-color: var(--accent-primary);">
                                <span>{{ $container->container_number }} [{{ $container->container_type }}]</span>
                            </label>
                            
                            <!-- Embedded loaded weight input, visible when checked -->
                            <div id="assignment-details-{{ $container->container_id }}" style="display: none; padding-left: 26px; gap: 16px; margin-top: 4px;">
                                <div style="flex: 1;">
                                    <label class="form-label" style="font-size: 0.725rem; margin-bottom: 4px;">Loaded Weight (kg)</label>
                                    <input type="number" 
                                           name="loaded_weights[{{ $container->container_id }}]" 
                                           placeholder="e.g. 15000" 
                                           min="0" 
                                           class="form-input" 
                                           style="padding: 6px 12px; font-size: 0.85rem; background-color: var(--bg-primary);">
                                </div>
                            </div>
                        </div>
                    @empty
                        <span style="color: var(--text-secondary); font-size: 0.9rem; font-style: italic;">No containers currently available for booking.</span>
                    @endforelse
                </div>
                @error('containers')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Live Freight Calculator Summary -->
            <div style="margin-top: 28px; margin-bottom: 24px; padding: 24px; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle); background-color: rgba(255, 255, 255, 0.02); display: flex; flex-direction: column; gap: 16px;">
                <h3 style="font-size: 1.1rem; font-family: 'Outfit', sans-serif; font-weight: 700; color: var(--border-focus); margin: 0; display: flex; align-items: center; gap: 8px;">
                    📊 Live Freight Calculator (Standard Rate)
                </h3>
                
                <div style="display: flex; flex-direction: column; gap: 10px; font-size: 0.9rem; color: var(--text-secondary);">
                    <div style="display: flex; justify-content: space-between;">
                        <span>Base Freight Fare:</span>
                        <span style="font-weight: 600; color: var(--text-primary);">৳5,000.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Container Charge (<span id="calc-containers-count">0</span> x ৳12,000):</span>
                        <span id="calc-containers-cost" style="font-weight: 600; color: var(--text-primary);">৳0.00</span>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>Cargo Weight Fee (<span id="calc-weight-sum">0</span> kg x ৳5):</span>
                        <span id="calc-weight-cost" style="font-weight: 600; color: var(--text-primary);">৳0.00</span>
                    </div>
                    <div style="height: 1px; background-color: rgba(255,255,255,0.08); margin: 8px 0;"></div>
                    <div style="display: flex; justify-content: space-between; font-size: 1.15rem; font-weight: 700; color: var(--accent-green);">
                        <span>Total Shipping Cost:</span>
                        <span id="calc-total-fare">৳5,000.00</span>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="form-group">
                <label class="form-label" for="notes">Shipment Notes & Special Instructions</label>
                <textarea id="notes" 
                          name="notes" 
                          class="form-control" 
                          placeholder="Provide shipment route details, cargo type description, temperature constraints, etc..." 
                          rows="4" 
                          style="resize: vertical;">{{ old('notes') }}</textarea>
                @error('notes')
                    <span class="alert alert-danger" style="margin-top: 8px; padding: 8px 12px; font-size: 0.8rem; display: block;">{{ $message }}</span>
                @enderror
            </div>

            <!-- Submit buttons -->
            <div style="display: flex; gap: 16px; margin-top: 28px;">
                <a href="{{ Auth::user()->role === 'CUSTOMER' ? '/customer/dashboard' : '/operator/dashboard' }}" class="btn-secondary" style="flex: 1; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center; height: auto;">Cancel</a>
                <button type="submit" class="btn-primary" style="flex: 2; height: auto; padding: 12px 24px;">
                    @if(Auth::user()->role === 'CUSTOMER')
                        💳 Proceed to Payment
                    @else
                        Confirm Booking
                    @endif
                </button>
            </div>
        </form>

    </div>
</div>

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const checkboxes = document.querySelectorAll('.container-checkbox');

        function recalculateCost() {
            let containerCount = 0;
            let totalWeight = 0;

            checkboxes.forEach(chk => {
                if (chk.checked) {
                    containerCount++;
                    const targetId = chk.getAttribute('data-target');
                    const detailsDiv = document.getElementById(targetId);
                    const weightInput = detailsDiv.querySelector('input[type="number"]');
                    if (weightInput) {
                        totalWeight += parseFloat(weightInput.value || 0);
                    }
                }
            });

            const baseFare = 5000;
            const containerCost = containerCount * 12000;
            const weightCost = totalWeight * 5;
            const totalCost = baseFare + containerCost + weightCost;

            document.getElementById('calc-containers-count').innerText = containerCount;
            document.getElementById('calc-containers-cost').innerText = '৳' + containerCost.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
            
            document.getElementById('calc-weight-sum').innerText = totalWeight.toLocaleString('en-US');
            document.getElementById('calc-weight-cost').innerText = '৳' + weightCost.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });

            document.getElementById('calc-total-fare').innerText = '৳' + totalCost.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        }
        
        checkboxes.forEach(chk => {
            chk.addEventListener('change', function() {
                const targetId = this.getAttribute('data-target');
                const detailsDiv = document.getElementById(targetId);
                const weightInput = detailsDiv.querySelector('input[type="number"]');
                
                if (this.checked) {
                    detailsDiv.style.display = 'flex';
                    if (weightInput) {
                        weightInput.required = true;
                        if (!weightInput.dataset.hasListener) {
                            weightInput.addEventListener('input', recalculateCost);
                            weightInput.dataset.hasListener = "true";
                        }
                    }
                } else {
                    detailsDiv.style.display = 'none';
                    detailsDiv.querySelectorAll('input').forEach(input => {
                        input.required = false;
                        input.value = '';
                    });
                }
                recalculateCost();
            });
        });

        // Initialize cost calculation
        recalculateCost();
    });
</script>
@endsection

@endsection
