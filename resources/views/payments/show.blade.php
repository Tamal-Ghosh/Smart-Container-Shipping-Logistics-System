@extends('layouts.app')

@section('title', 'Invoice Details — Smart Shipping')

@section('styles')
<style>
    @media print {
        nav.main-navbar, .no-print {
            display: none !important;
        }
        .dashboard-wrapper {
            margin: 0 !important;
            padding: 0 !important;
            background-color: #fff !important;
            color: #000 !important;
        }
        .invoice-card {
            border: none !important;
            box-shadow: none !important;
            background: #fff !important;
            color: #000 !important;
            padding: 0 !important;
        }
        body {
            background-color: #fff !important;
            color: #000 !important;
        }
    }
</style>
@endsection

@section('content')

<div class="dashboard-wrapper">

    <div class="no-print" style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <a href="/payments" class="btn-secondary" style="width: auto; padding: 8px 16px; font-size: 0.85rem;">
            ⬅ Back to Registry
        </a>

        <button onclick="window.print()" class="btn-primary" style="width: auto; padding: 8px 16px; font-size: 0.85rem; background-color: var(--border-focus); border-color: var(--border-focus);">
            🖨️ Print / Save PDF
        </button>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 24px;">{{ session('success') }}</div>
    @endif

    <div class="dashboard-table-card invoice-card" style="max-width: 800px; margin: 0 auto; padding: 48px 40px; position: relative; border-top: 6px solid var(--border-focus);">
        
        <!-- Invoice Header -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 24px; border-bottom: 1px solid var(--border-subtle); padding-bottom: 32px; margin-bottom: 32px;">
            <div>
                <h1 style="font-family: 'Outfit', sans-serif; font-size: 1.8rem; font-weight: 700; margin: 0 0 4px 0; color: var(--border-focus);">INVOICE</h1>
                <span style="font-size: 0.875rem; color: var(--text-muted); font-weight: 600;">INV-#2026-{{ str_pad($payment->payment_id, 4, '0', STR_PAD_LEFT) }}</span>
            </div>

            <div style="text-align: right;">
                <h2 style="font-size: 1.2rem; font-weight: 700; margin: 0 0 4px 0;">Smart Shipping Ltd.</h2>
                <p style="font-size: 0.825rem; color: var(--text-secondary); margin: 0; line-height: 1.4;">
                    Port Authority Building, terminal 3A<br>
                    Chittagong, Bangladesh<br>
                    billing@smartshipping.com
                </p>
            </div>
        </div>

        <!-- Billed To & Shipment Specs Info -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 32px; margin-bottom: 40px;">
            <div>
                <h3 style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 12px;">Billed To</h3>
                <strong style="font-size: 1.05rem; display: block; margin-bottom: 6px;">{{ $payment->customer->company_name ?? 'N/A' }}</strong>
                <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0; line-height: 1.5;">
                    Contact: {{ $payment->customer->contact_person ?? 'N/A' }}<br>
                    Address: {{ $payment->customer->address ?? 'N/A' }}<br>
                    Country: {{ $payment->customer->country ?? 'N/A' }}<br>
                    Phone: {{ $payment->customer->phone ?? 'N/A' }}
                </p>
            </div>

            <div>
                <h3 style="font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.05em; color: var(--text-muted); margin-bottom: 12px;">Shipment Details</h3>
                <p style="font-size: 0.875rem; color: var(--text-secondary); margin: 0; line-height: 1.6;">
                    <strong>Reference:</strong> {{ $payment->shipment->shipment_ref ?? 'N/A' }}<br>
                    <strong>Route:</strong> {{ $payment->shipment->sourcePort->port_name ?? 'N/A' }} ➔ {{ $payment->shipment->destinationPort->port_name ?? 'N/A' }}<br>
                    <strong>Cargo Status:</strong> {{ $payment->shipment->status ?? 'N/A' }}
                </p>
            </div>
        </div>

        <!-- Payment Meta Grid -->
        <div style="background-color: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); border-radius: var(--radius-xs); padding: 24px; margin-bottom: 40px; display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 16px;">
            <div>
                <span style="font-size: 0.725rem; color: var(--text-muted); text-transform: uppercase; display: block; font-weight: 700; margin-bottom: 4px;">Status</span>
                <span class="badge badge-{{ strtolower($payment->payment_status) }}">
                    {{ $payment->payment_status }}
                </span>
            </div>
            <div>
                <span style="font-size: 0.725rem; color: var(--text-muted); text-transform: uppercase; display: block; font-weight: 700; margin-bottom: 4px;">Method</span>
                <strong style="font-size: 0.95rem;">{{ str_replace('_', ' ', $payment->payment_method) }}</strong>
            </div>
            <div>
                <span style="font-size: 0.725rem; color: var(--text-muted); text-transform: uppercase; display: block; font-weight: 700; margin-bottom: 4px;">Due Date</span>
                <strong style="font-size: 0.95rem;">{{ $payment->due_date ? \Carbon\Carbon::parse($payment->due_date)->format('M d, Y') : 'N/A' }}</strong>
            </div>
            <div>
                <span style="font-size: 0.725rem; color: var(--text-muted); text-transform: uppercase; display: block; font-weight: 700; margin-bottom: 4px;">Payment Date</span>
                <strong style="font-size: 0.95rem;">{{ $payment->payment_date ? \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') : 'N/A' }}</strong>
            </div>
        </div>

        <!-- Transaction Table / Line Items -->
        <div style="margin-bottom: 40px;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 2px solid var(--border-subtle);">
                        <th style="padding: 12px 0; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted);">Description</th>
                        <th style="padding: 12px 0; text-align: right; font-size: 0.8rem; text-transform: uppercase; color: var(--text-muted);">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid var(--border-subtle);">
                        <td style="padding: 18px 0; font-size: 0.95rem;">
                            <strong>Ocean Cargo Shipment Booking Freight Charges</strong><br>
                            <span style="font-size: 0.8rem; color: var(--text-secondary);">Ref: {{ $payment->shipment->shipment_ref ?? 'N/A' }}</span>
                        </td>
                        <td style="padding: 18px 0; text-align: right; font-weight: 700; font-size: 1.1rem;">
                            ৳{{ number_format($payment->amount, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Total & Transaction Info -->
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 24px;">
            <div>
                @if($payment->transaction_ref)
                    <div style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">
                        <strong>Transaction Reference:</strong><br>
                        <span style="font-family: monospace; font-size: 0.95rem; color: var(--accent-green); font-weight: 700;">{{ $payment->transaction_ref }}</span>
                    </div>
                @else
                    <span style="font-size: 0.85rem; color: var(--text-muted); font-style: italic;">No payment transaction reference generated yet.</span>
                @endif
            </div>

            <div style="text-align: right; min-width: 200px;">
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 8px;">
                    <span style="color: var(--text-muted);">Subtotal:</span>
                    <strong>৳{{ number_format($payment->amount, 2) }}</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 0.9rem; margin-bottom: 12px;">
                    <span style="color: var(--text-muted);">Tax / VAT (0%):</span>
                    <strong>৳0.00</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 1.25rem; border-top: 1px solid var(--border-focus); padding-top: 12px; color: var(--border-focus);">
                    <span>Total Due:</span>
                    <strong>৳{{ number_format($payment->amount, 2) }}</strong>
                </div>
            </div>
        </div>

        <!-- Customer Actions -->
        @if(Auth::user()->role === 'CUSTOMER' && $payment->payment_status === 'PENDING')
            <div class="no-print" style="margin-top: 48px; border-top: 1px solid var(--border-subtle); padding-top: 32px; display: flex; justify-content: flex-end;">
                <a href="/payments/{{ $payment->payment_id }}/checkout" class="btn-primary" style="width: auto; padding: 10px 24px; font-size: 0.9rem; background-color: var(--accent-green); border-color: var(--accent-green); color: #fff; font-weight: 600; text-decoration: none; border-radius: var(--radius-sm); display: inline-flex; align-items: center; justify-content: center; height: auto; transition: opacity 0.2s ease;">
                    💳 Pay Invoice Now (Mock)
                </a>
            </div>
        @endif

        <!-- Admin / Operator Action Buttons -->
        @if((Auth::user()->role === 'ADMIN' || Auth::user()->role === 'OPERATOR') && $payment->payment_status !== 'COMPLETED' && $payment->payment_status !== 'REFUNDED')
            <div class="no-print" style="margin-top: 48px; border-top: 1px solid var(--border-subtle); padding-top: 32px; display: flex; justify-content: flex-end; gap: 12px; flex-wrap: wrap;">
                
                <form method="POST" action="/payments/{{ $payment->payment_id }}/status" style="margin: 0;">
                    @csrf
                    <input type="hidden" name="action" value="fail">
                    <button type="submit" class="btn-secondary" style="width: auto; padding: 10px 20px; font-size: 0.85rem; color: #f87171; border-color: rgba(239, 68, 68, 0.4);" onclick="return confirm('Mark this invoice as failed?')">
                        ⚠️ Fail Invoice
                    </button>
                </form>

                <form method="POST" action="/payments/{{ $payment->payment_id }}/status" style="margin: 0;">
                    @csrf
                    <input type="hidden" name="action" value="pay">
                    <button type="submit" class="btn-primary" style="width: auto; padding: 10px 20px; font-size: 0.85rem; background-color: var(--accent-green); border-color: var(--accent-green); color: #fff;">
                        ✔️ Mark as Paid
                    </button>
                </form>

            </div>
        @endif

        @if(Auth::user()->role === 'ADMIN' && $payment->payment_status === 'COMPLETED')
            <div class="no-print" style="margin-top: 48px; border-top: 1px solid var(--border-subtle); padding-top: 32px; display: flex; justify-content: flex-end;">
                
                <form method="POST" action="/payments/{{ $payment->payment_id }}/status" style="margin: 0;">
                    @csrf
                    <input type="hidden" name="action" value="refund">
                    <button type="submit" class="btn-secondary" style="width: auto; padding: 10px 20px; font-size: 0.85rem; color: #fbbf24; border-color: rgba(245, 158, 11, 0.4);" onclick="return confirm('Are you sure you want to issue a refund for this invoice?')">
                        🔙 Issue Refund
                    </button>
                </form>

            </div>
        @endif

    </div>

</div>

@endsection
