@extends('layouts.app')

@section('title', 'Shipment Tracking Timeline — Smart Shipping')

@section('content')

<div class="dashboard-wrapper">

    <div style="margin-bottom: 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px;">
        <div>
            <span style="font-size: 0.725rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em; display: block;">Tracking Cargo Shipment</span>
            <h1 style="font-family: 'Outfit', sans-serif; font-size: 1.8rem; color: var(--border-focus); margin-top: 4px;">Timeline for {{ $shipment->shipment_ref }}</h1>
        </div>
        
        <div>
            <a href="/operator/shipments/{{ $shipment->shipment_id }}" class="btn-secondary" style="width: auto; padding: 10px 20px;">
                View Shipment Specs
            </a>
        </div>
    </div>

    <!-- Timeline Wrapper Card -->
    <div style="max-width: 800px; margin: 0 auto;">
        
        <!-- Tracking status summary banner -->
        <div class="dashboard-table-card" style="padding: 24px; margin-bottom: 32px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; border-left: 4px solid var(--border-focus);">
            <div>
                <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; display: block;">Route Journey</span>
                <strong style="font-size: 1.1rem; color: var(--text-primary);">
                    {{ $shipment->sourcePort->port_name ?? 'N/A' }} ({{ $shipment->sourcePort->port_code ?? 'N/A' }}) 
                    ➔ 
                    {{ $shipment->destinationPort->port_name ?? 'N/A' }} ({{ $shipment->destinationPort->port_code ?? 'N/A' }})
                </strong>
            </div>

            <div style="text-align: right;">
                <span style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 4px;">Current Status</span>
                <span class="badge badge-{{ strtolower($shipment->status) }}">
                    {{ $shipment->status }}
                </span>
            </div>
        </div>

        <!-- Vertical Timeline Card -->
        <div class="dashboard-table-card" style="padding: 36px 32px;">
            <h2 class="dashboard-table-title" style="margin-bottom: 28px;">🛰️ Full Event Logs Timeline (PL/SQL view)</h2>

            <div class="timeline-feed" style="position: relative; padding-left: 8px;">
                @forelse($timeline as $event)
                    <div class="timeline-item" style="padding-bottom: 32px; position: relative;">
                        
                        <!-- Event Details Header -->
                        <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 16px;">
                            <div>
                                <h3 style="font-family: 'Outfit', sans-serif; font-size: 1.1rem; font-weight: 700; color: var(--border-focus); margin: 0;">
                                    {{ $event->event_type }}
                                </h3>
                                <p style="font-size: 0.9rem; color: var(--text-primary); margin: 6px 0 4px 0;">
                                    📍 <strong>Location:</strong> {{ $event->location }} 
                                    @if($event->port_name)
                                        <span style="color: var(--text-secondary);">({{ $event->port_name }})</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <!-- Remarks -->
                        @if($event->remarks)
                            <div style="background-color: rgba(255, 255, 255, 0.02); border: 1px solid var(--border-subtle); padding: 12px 16px; border-radius: var(--radius-xs); margin-top: 8px; margin-bottom: 8px;">
                                <p style="font-size: 0.85rem; color: var(--text-secondary); margin: 0; line-height: 1.45; font-style: italic;">
                                    "{{ $event->remarks }}"
                                </p>
                            </div>
                        @endif

                        <!-- Date/Time stamp -->
                        <span style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-top: 6px;">
                            📅 {{ \Carbon\Carbon::parse($event->updated_at)->format('l, F d, Y — h:i A') }}
                        </span>

                    </div>
                @empty
                    <div style="text-align: center; padding: 48px; color: var(--text-secondary);">
                        <svg viewBox="0 0 24 24" width="48" height="48" stroke="currentColor" stroke-width="1.5" fill="none" stroke-linecap="round" stroke-linejoin="round" style="margin-bottom: 16px; color: var(--text-muted); opacity: 0.5;">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                        <p style="font-size: 0.95rem; font-style: italic; margin: 0;">No tracking checkpoint logs have been submitted for this shipment reference yet.</p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>

</div>

@endsection
