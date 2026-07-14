@extends('layouts.app')

@section('title', 'Create Payment Invoice — Smart Shipping')

@section('content')

<div class="dashboard-wrapper">

    <div style="max-width: 600px; margin: 0 auto;">
        
        <div style="margin-bottom: 24px;">
            <a href="/payments" class="btn-secondary" style="width: auto; padding: 8px 16px; font-size: 0.85rem;">
                ⬅ Back to Registry
            </a>
        </div>

        <div class="dashboard-table-card" style="padding: 36px 32px;">
            <h2 class="dashboard-table-title" style="margin-bottom: 28px; font-size: 1.4rem;">➕ Create Payment Invoice</h2>

            @if($errors->any())
                <div class="alert alert-danger" style="margin-bottom: 24px; padding: 12px; border-radius: var(--radius-xs);">
                    <ul style="margin: 0; padding-left: 16px; font-size: 0.875rem;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="/payments">
                @csrf

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" for="shipment_id">Select Shipment</label>
                    <select name="shipment_id" id="shipment_id" required class="form-input" style="background-color: var(--bg-primary);">
                        <option value="">-- Choose Shipment --</option>
                        @foreach($shipments as $shipment)
                            <option value="{{ $shipment->shipment_id }}" {{ old('shipment_id') == $shipment->shipment_id ? 'selected' : '' }}>
                                {{ $shipment->shipment_ref }} (Customer: {{ $shipment->customer->company_name ?? 'N/A' }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" for="amount">Invoice Amount (৳)</label>
                    <input type="number" step="0.01" name="amount" id="amount" value="{{ old('amount') }}" required class="form-input" placeholder="e.g. 5500.00">
                </div>

                <div class="form-group" style="margin-bottom: 20px;">
                    <label class="form-label" for="payment_method">Preferred Payment Method</label>
                    <select name="payment_method" id="payment_method" required class="form-input" style="background-color: var(--bg-primary);">
                        <option value="CREDIT_CARD" {{ old('payment_method') === 'CREDIT_CARD' ? 'selected' : '' }}>Credit Card</option>
                        <option value="BANK_TRANSFER" {{ old('payment_method') === 'BANK_TRANSFER' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="CASH" {{ old('payment_method') === 'CASH' ? 'selected' : '' }}>Cash</option>
                        <option value="MOBILE_BANKING" {{ old('payment_method') === 'MOBILE_BANKING' ? 'selected' : '' }}>Mobile Banking</option>
                    </select>
                </div>

                <div class="form-group" style="margin-bottom: 28px;">
                    <label class="form-label" for="due_date">Due Date</label>
                    <input type="date" name="due_date" id="due_date" value="{{ old('due_date', date('Y-m-d', strtotime('+7 days'))) }}" required class="form-input">
                </div>

                <button type="submit" class="btn-primary" style="padding: 12px; font-size: 1rem; font-weight: 600;">
                    Generate Payment Invoice (PL/SQL)
                </button>

            </form>
        </div>

    </div>

</div>

@endsection
