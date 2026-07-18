@extends('layouts.app')

@section('title', 'Manage Ports — Smart Shipping')

@section('content')

<div class="dashboard-wrapper">

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 24px;">{{ session('success') }}</div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 32px;">
        <form method="GET" action="/ports" style="display: flex; gap: 12px; flex: 1; max-width: 500px;">
            <input type="text" name="search" placeholder="Search by name, code or country..." value="{{ request('search') }}" class="form-input">
            <button type="submit" class="btn-primary" style="width: auto; padding: 10px 24px;">Search</button>
        </form>
        @if(Auth::user()->role === 'ADMIN')
            <a href="/ports/create" class="btn-primary" style="width: auto; padding: 12px 24px; display: inline-flex; align-items: center; gap: 8px;">
                <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"></line>
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                </svg>
                Add New Port
            </a>
        @endif
    </div>

    <div class="dashboard-table-card">
        <h2 class="dashboard-table-title">Port Registry</h2>
        <div class="table-responsive">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Port Name</th>
                        <th>Location</th>
                        <th>Country</th>
                        <th>Status</th>
                        @if(Auth::user()->role === 'ADMIN')
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($ports as $port)
                        <tr>
                            <td style="font-weight: 700; color: var(--border-focus);">{{ $port->port_code }}</td>
                            <td style="font-weight: 600;">{{ $port->port_name }}</td>
                            <td>{{ $port->location ?? 'N/A' }}</td>
                            <td>{{ $port->country ?? 'N/A' }}</td>
                            <td>
                                <span class="badge badge-{{ strtolower($port->status) }}">
                                    {{ $port->status }}
                                </span>
                            </td>
                            <td>
                                <div style="display: flex; gap: 12px; align-items: center;">
                                    <a href="/ports/{{ $port->port_id }}/edit" class="btn-secondary" style="width: auto; padding: 6px 14px; font-size: 0.8rem; border-radius: var(--radius-xs);">
                                        Edit
                                    </a>
                                    <form method="POST" action="/ports/{{ $port->port_id }}/toggle" style="margin: 0;">
                                        @csrf
                                        <button type="submit" class="btn-secondary" style="width: auto; padding: 6px 14px; font-size: 0.8rem; border-radius: var(--radius-xs); border-color: rgba(255, 255, 255, 0.08); color: var(--text-secondary);">
                                            {{ $port->status === 'ACTIVE' ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 32px; color: var(--text-secondary);">
                                No ports found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
