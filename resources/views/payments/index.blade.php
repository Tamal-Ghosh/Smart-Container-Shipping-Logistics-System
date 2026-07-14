@extends('layouts.app')

@section('title', 'Payments Registry — Smart Shipping')

@section('content')

<div class="dashboard-wrapper">

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 24px;">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 24px;">{{ session('error') }}</div>
    @endif

    <!-- Revenue Calculator (Admin / Operator only) -->
    @if(Auth::user()->role !== 'CUSTOMER')
        <div class="dashboard-table-card" style="padding: 24px; margin-bottom: 32px; border-left: 4px solid var(--accent-green);">
            <h3 class="dashboard-table-title" style="font-size: 1rem; margin-bottom: 8px; color: var(--accent-green);">💰 Revenue Calculator (PL/SQL)</h3>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 16px;">Calculate total completed revenue between any two dates using database aggregates.</p>
            
            <form method="GET" action="/payments" style="display: flex; gap: 16px; align-items: end; flex-wrap: wrap;">
                <!-- Keep existing search and filters active -->
                @foreach(request()->except(['rev_start', 'rev_end']) as $key => $val)
                    <input type="hidden" name="{{ $key }}" value="{{ $val }}">
                @endforeach

                <div>
                    <label class="form-label" style="font-size: 0.725rem;">Start Date</label>
                    <input type="date" name="rev_start" value="{{ request('rev_start', date('Y-01-01')) }}" required class="form-input" style="width: 150px;">
                </div>

                <div>
                    <label class="form-label" style="font-size: 0.725rem;">End Date</label>
                    <input type="date" name="rev_end" value="{{ request('rev_end', date('Y-12-31')) }}" required class="form-input" style="width: 150px;">
                </div>

                <div style="display: flex; gap: 8px;">
                    <button type="submit" class="btn-primary" style="padding: 10px 20px; font-size: 0.85rem; background-color: var(--accent-green); border-color: var(--accent-green);">Calculate Revenue</button>
                    @if(isset($revenue))
                        <a href="/payments" class="btn-secondary" style="padding: 10px 20px; font-size: 0.85rem;">Clear</a>
                    @endif
                </div>

                @if(isset($revenue))
                    <div style="margin-left: 16px; display: flex; flex-direction: column; justify-content: center;">
                        <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700;">Calculated Revenue</span>
                        <strong style="font-size: 1.4rem; color: var(--accent-green);">৳{{ number_format($revenue, 2) }}</strong>
                    </div>
                @endif
            </form>
        </div>
    @endif

    <!-- Filtering panel -->
    <div class="dashboard-table-card" style="padding: 24px; margin-bottom: 32px;">
        <h3 class="dashboard-table-title" style="font-size: 1rem; margin-bottom: 16px;">🔍 Filter & Search Payments</h3>
        <form method="GET" action="/payments" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 16px; align-items: end;">
            
            <!-- Maintain revenue params if active -->
            @if(request('rev_start'))
                <input type="hidden" name="rev_start" value="{{ request('rev_start') }}">
                <input type="hidden" name="rev_end" value="{{ request('rev_end') }}">
            @endif



            <div>
                <label class="form-label" style="font-size: 0.725rem;">Method</label>
                <select name="method" class="form-input" style="background-color: var(--bg-primary);">
                    <option value="">All Methods</option>
                    <option value="CREDIT_CARD" {{ request('method') === 'CREDIT_CARD' ? 'selected' : '' }}>CREDIT CARD</option>
                    <option value="BANK_TRANSFER" {{ request('method') === 'BANK_TRANSFER' ? 'selected' : '' }}>BANK TRANSFER</option>
                    <option value="CASH" {{ request('method') === 'CASH' ? 'selected' : '' }}>CASH</option>
                    <option value="MOBILE_BANKING" {{ request('method') === 'MOBILE_BANKING' ? 'selected' : '' }}>MOBILE BANKING</option>
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 0.725rem;">Min Amount (৳)</label>
                <input type="number" name="amount_min" placeholder="Min..." value="{{ request('amount_min') }}" class="form-input">
            </div>

            <div>
                <label class="form-label" style="font-size: 0.725rem;">Max Amount (৳)</label>
                <input type="number" name="amount_max" placeholder="Max..." value="{{ request('amount_max') }}" class="form-input">
            </div>



            <div style="display: flex; gap: 8px;">
                <a href="/payments" class="btn-secondary" style="flex: 1; padding: 10px; font-size: 0.85rem; text-align: center;">Reset</a>
                <button type="submit" class="btn-primary" style="flex: 2; padding: 10px; font-size: 0.85rem;">Filter</button>
            </div>

        </form>
    </div>

    <!-- Payments Registry Table -->
    <div class="dashboard-table-card">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 20px;">
            <h2 class="dashboard-table-title" style="margin-bottom: 0;">💵 Invoices & Payments (PL/SQL view)</h2>
            
            @if(Auth::user()->role === 'ADMIN' || Auth::user()->role === 'OPERATOR')
                <a href="/payments/create" class="btn-primary" style="width: auto; padding: 10px 20px; font-size: 0.85rem;">
                    ➕ Create Invoice
                </a>
            @endif
        </div>

        <div class="table-responsive">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Shipment Ref</th>
                        <th>Customer Company</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Due Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $payment)
                        <tr data-href="/payments/{{ $payment->payment_id }}">
                            <td style="font-weight: 700; color: var(--text-muted);">#{{ $payment->payment_id }}</td>
                            <td style="font-weight: 700; color: var(--border-focus);">{{ $payment->shipment_ref }}</td>
                            <td style="font-weight: 600;">{{ $payment->company_name }}</td>
                            <td style="font-weight: 700; color: var(--text-primary);">৳{{ number_format($payment->amount, 2) }}</td>
                            <td>{{ str_replace('_', ' ', $payment->payment_method) }}</td>
                            <td>
                                {{ $payment->due_date ? \Carbon\Carbon::parse($payment->due_date)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td>
                                <span class="badge badge-{{ strtolower($payment->payment_status) }}">
                                    {{ $payment->payment_status }}
                                </span>
                            </td>
                            <td>
                                <a href="/payments/{{ $payment->payment_id }}" class="btn-secondary" style="width: auto; padding: 6px 14px; font-size: 0.8rem; border-radius: var(--radius-xs);">
                                    View Invoice
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                                No payment invoices found matching the query.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
