@extends('layouts.app')

@section('title', 'Secure Payment Gateway — Smart Shipping')

@section('styles')
<style>
    .checkout-container {
        max-width: 650px;
        margin: 40px auto;
        padding: 0 20px;
    }

    .checkout-card {
        background: var(--bg-card, #1e293b);
        border: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
        border-radius: var(--radius-md, 12px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
        overflow: hidden;
    }

    .checkout-header {
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        padding: 32px 32px 24px 32px;
        border-bottom: 1px solid var(--border-subtle, rgba(255, 255, 255, 0.08));
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .checkout-title {
        font-family: 'Outfit', sans-serif;
        font-size: 1.35rem;
        font-weight: 700;
        margin: 0;
        color: var(--text-primary);
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .checkout-amount-badge {
        background-color: rgba(16, 185, 129, 0.15);
        color: #10b981;
        padding: 8px 16px;
        border-radius: 30px;
        font-weight: 700;
        font-size: 1.15rem;
        border: 1px solid rgba(16, 185, 129, 0.25);
    }

    .checkout-body {
        padding: 32px;
    }

    .invoice-brief {
        background-color: rgba(255, 255, 255, 0.02);
        border-radius: 8px;
        padding: 16px;
        margin-bottom: 28px;
        border-left: 4px solid var(--border-focus, #3b82f6);
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 12px;
    }

    .brief-item {
        display: flex;
        flex-direction: column;
    }

    .brief-label {
        font-size: 0.725rem;
        color: var(--text-muted, #94a3b8);
        text-transform: uppercase;
        letter-spacing: 0.05em;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .brief-value {
        font-size: 0.9rem;
        color: var(--text-primary);
        font-weight: 600;
    }

    /* Tab Switcher */
    .payment-tabs {
        display: flex;
        gap: 12px;
        margin-bottom: 28px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        padding-bottom: 12px;
    }

    .payment-tab {
        flex: 1;
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        padding: 14px;
        border-radius: 8px;
        color: var(--text-secondary);
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .payment-tab:hover {
        background: rgba(255, 255, 255, 0.05);
        color: var(--text-primary);
    }

    .payment-tab.active {
        background: rgba(59, 130, 246, 0.08);
        border-color: var(--border-focus, #3b82f6);
        color: var(--border-focus, #3b82f6);
        box-shadow: 0 0 10px rgba(59, 130, 246, 0.1);
    }

    /* Form Fields */
    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
        animation: fadeIn 0.3s ease-in-out;
    }

    .mfs-provider-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 20px;
    }

    .mfs-provider-card {
        background: rgba(255, 255, 255, 0.02);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 8px;
        padding: 16px;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 8px;
        transition: all 0.2s ease;
    }

    .mfs-provider-card input[type="radio"] {
        display: none;
    }

    .mfs-provider-card:hover {
        background: rgba(255, 255, 255, 0.05);
    }

    .mfs-provider-card.selected {
        border-color: #ec4899; /* bKash pink vibe default */
        background: rgba(236, 72, 153, 0.05);
    }

    .mfs-provider-card.selected.nagad {
        border-color: #f97316; /* Nagad orange */
        background: rgba(249, 115, 22, 0.05);
    }

    .mfs-provider-card.selected.rocket {
        border-color: #8b5cf6; /* Rocket purple */
        background: rgba(139, 92, 246, 0.05);
    }

    .provider-logo {
        height: 32px;
        object-fit: contain;
        filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.2));
    }

    .provider-name {
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: capitalize;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }

    /* Screen Loading Overlay */
    .payment-loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(15, 23, 42, 0.95);
        z-index: 9999;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        pointer-events: none;
        transition: opacity 0.3s ease-in-out;
    }

    .payment-loading-overlay.active {
        opacity: 1;
        pointer-events: auto;
    }

    .spinner-ring {
        display: inline-block;
        width: 64px;
        height: 64px;
        border: 4px solid rgba(59, 130, 246, 0.1);
        border-radius: 50%;
        border-top-color: var(--border-focus, #3b82f6);
        animation: spin 1s ease-in-out infinite;
        margin-bottom: 24px;
    }

    .loading-status-text {
        font-family: 'Outfit', sans-serif;
        font-size: 1.2rem;
        color: var(--text-primary);
        font-weight: 600;
        letter-spacing: 0.02em;
    }

    .loading-subtext {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-top: 8px;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection

@section('content')

<div class="checkout-container">

    @if($errors->any())
        <div class="alert alert-danger" style="margin-bottom: 24px; border-radius: var(--radius-sm);">
            <ul style="margin: 0; padding-left: 16px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="checkout-card">
        
        <div class="checkout-header">
            <div>
                <h1 class="checkout-title">
                    <span>🔒 Secure Checkout</span>
                </h1>
                <p style="margin: 4px 0 0 0; font-size: 0.825rem; color: var(--text-muted);">Smart Shipping Invoice Portal</p>
            </div>
            <div class="checkout-amount-badge">
                ৳{{ number_format($payment->amount, 2) }}
            </div>
        </div>

        <div class="checkout-body">

            <div class="invoice-brief">
                <div class="brief-item">
                    <span class="brief-label">Invoice Ref</span>
                    <span class="brief-value">INV-#2026-{{ str_pad($payment->payment_id, 4, '0', STR_PAD_LEFT) }}</span>
                </div>
                <div class="brief-item">
                    <span class="brief-label">Shipment Code</span>
                    <span class="brief-value">{{ $payment->shipment->shipment_ref }}</span>
                </div>
                <div class="brief-item" style="grid-column: span 2;">
                    <span class="brief-label">Billed To</span>
                    <span class="brief-value">{{ $payment->customer->company_name }}</span>
                </div>
            </div>

            <!-- Tab Headers -->
            <div class="payment-tabs">
                <button type="button" class="payment-tab active" onclick="switchTab('card')">
                    💳 Credit / Debit Card
                </button>
                <button type="button" class="payment-tab" onclick="switchTab('mfs')">
                    📱 Mobile Banking
                </button>
            </div>

            <form id="checkout-form" method="POST" action="/payments/{{ $payment->payment_id }}/checkout">
                @csrf
                <input type="hidden" name="payment_method" id="selected-method" value="CREDIT_CARD">

                <!-- Card Content -->
                <div id="card-tab" class="tab-content active">
                    <div class="form-group">
                        <label class="form-label" for="card_name">Cardholder Name</label>
                        <input type="text" id="card_name" name="card_name" placeholder="John Doe" class="form-control" value="{{ old('card_name', Auth::user()->username) }}">
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="card_number">Card Number</label>
                        <input type="text" id="card_number" name="card_number" placeholder="4111 2222 3333 4444" maxlength="19" class="form-control" value="{{ old('card_number') }}">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="card_expiry">Expiry Date</label>
                            <input type="text" id="card_expiry" name="card_expiry" placeholder="MM/YY" maxlength="5" class="form-control" value="{{ old('card_expiry') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="card_cvv">CVV</label>
                            <input type="password" id="card_cvv" name="card_cvv" placeholder="•••" maxlength="3" class="form-control" value="{{ old('card_cvv') }}">
                        </div>
                    </div>
                </div>

                <!-- Mobile Banking Content -->
                <div id="mfs-tab" class="tab-content">
                    <label class="form-label" style="margin-bottom: 12px; display: block;">Select Mobile Account Provider</label>
                    <div class="mfs-provider-grid">
                        
                        <label class="mfs-provider-card selected bkash">
                            <input type="radio" name="provider" value="bkash" checked onclick="selectProvider(this, 'bkash')">
                            <!-- Draw simple bKash icon fallback text or SVG if needed, let's use stylized text -->
                            <span class="provider-name" style="color: #ec4899; font-size: 1rem;">bKash</span>
                        </label>

                        <label class="mfs-provider-card nagad">
                            <input type="radio" name="provider" value="nagad" onclick="selectProvider(this, 'nagad')">
                            <span class="provider-name" style="color: #f97316; font-size: 1rem;">Nagad</span>
                        </label>

                        <label class="mfs-provider-card rocket">
                            <input type="radio" name="provider" value="rocket" onclick="selectProvider(this, 'rocket')">
                            <span class="provider-name" style="color: #8b5cf6; font-size: 1rem;">Rocket</span>
                        </label>

                    </div>

                    <div class="form-group">
                        <label class="form-label" for="phone_number">Wallet Number</label>
                        <input type="text" id="phone_number" name="phone_number" placeholder="017XXXXXXXX" maxlength="15" class="form-control" value="{{ old('phone_number') }}">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="otp">Enter 6-Digit OTP</label>
                            <input type="text" id="otp" name="otp" placeholder="123456" maxlength="6" class="form-control" value="{{ old('otp') }}">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="pin">Wallet PIN</label>
                            <input type="password" id="pin" name="pin" placeholder="••••" maxlength="5" class="form-control" value="{{ old('pin') }}">
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 16px; margin-top: 32px;">
                    <a href="/payments/{{ $payment->payment_id }}" class="btn-secondary" style="flex: 1; text-align: center; text-decoration: none; display: flex; align-items: center; justify-content: center;">Cancel</a>
                    <button type="submit" class="btn-primary" style="flex: 2; height: 46px;">
                        Authorize Payment (Mock)
                    </button>
                </div>
            </form>

        </div>
    </div>
</div>

<!-- Beautiful payment loading overlay -->
<div class="payment-loading-overlay" id="loading-overlay">
    <div class="spinner-ring"></div>
    <div class="loading-status-text" id="status-text">Connecting to Payment Gateway...</div>
    <div class="loading-subtext" id="sub-status-text">Do not refresh this page or close the window.</div>
</div>

@endsection

@section('scripts')
<script>
    function switchTab(type) {
        document.querySelectorAll('.payment-tab').forEach(tab => tab.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(content => content.classList.remove('active'));

        const methodInput = document.getElementById('selected-method');

        if (type === 'card') {
            document.querySelectorAll('.payment-tab')[0].classList.add('active');
            document.getElementById('card-tab').classList.add('active');
            methodInput.value = 'CREDIT_CARD';
            
            // Toggle required attributes
            document.getElementById('card_name').required = true;
            document.getElementById('card_number').required = true;
            document.getElementById('card_expiry').required = true;
            document.getElementById('card_cvv').required = true;

            document.getElementById('phone_number').required = false;
            document.getElementById('otp').required = false;
            document.getElementById('pin').required = false;
        } else {
            document.querySelectorAll('.payment-tab')[1].classList.add('active');
            document.getElementById('mfs-tab').classList.add('active');
            methodInput.value = 'MOBILE_BANKING';

            // Toggle required attributes
            document.getElementById('card_name').required = false;
            document.getElementById('card_number').required = false;
            document.getElementById('card_expiry').required = false;
            document.getElementById('card_cvv').required = false;

            document.getElementById('phone_number').required = true;
            document.getElementById('otp').required = true;
            document.getElementById('pin').required = true;
        }
    }

    function selectProvider(radioInput, providerName) {
        document.querySelectorAll('.mfs-provider-card').forEach(card => {
            card.classList.remove('selected', 'bkash', 'nagad', 'rocket');
        });
        
        const cardLabel = radioInput.closest('.mfs-provider-card');
        cardLabel.classList.add('selected', providerName);
    }

    // Initialize required fields
    document.addEventListener('DOMContentLoaded', function() {
        switchTab('card');

        // Form submission loading simulation
        const form = document.getElementById('checkout-form');
        const overlay = document.getElementById('loading-overlay');
        const statusText = document.getElementById('status-text');
        const subStatusText = document.getElementById('sub-status-text');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Basic validation check before starting simulation
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            overlay.classList.add('active');
            
            setTimeout(() => {
                statusText.innerText = "Securing network session...";
            }, 1200);

            setTimeout(() => {
                statusText.innerText = "Authorizing mock funds transfer...";
                subStatusText.innerText = "Verifying transaction signature with the bank...";
            }, 2500);

            setTimeout(() => {
                statusText.innerText = "Recording database receipt...";
                subStatusText.innerText = "Finalizing invoice completion record...";
            }, 4000);

            setTimeout(() => {
                form.submit();
            }, 5500);
        });
    });
</script>
@endsection
