@extends('layouts.app')

@section('title', 'Manage Containers — Smart Shipping')

@section('content')

<div class="dashboard-wrapper">

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 24px;">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 24px;">{{ session('error') }}</div>
    @endif

    @if(session('info'))
        <div class="alert alert-info" style="margin-bottom: 24px;">{{ session('info') }}</div>
    @endif

    <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 32px;">
        <form method="GET" action="/containers" style="display: flex; gap: 12px; flex: 1; max-width: 700px; flex-wrap: wrap;">
            <input type="text" name="search" placeholder="Search by number, type or status..." value="{{ request('search') }}" class="form-input" style="flex: 2; min-width: 200px;">
            
            <select name="type" class="form-input" style="flex: 1; min-width: 120px;">
                <option value="">All Types</option>
                <option value="NORMAL" {{ request('type') === 'NORMAL' ? 'selected' : '' }}>NORMAL</option>
                <option value="FREEZE" {{ request('type') === 'FREEZE' ? 'selected' : '' }}>FREEZE</option>
                <option value="CHEMICAL" {{ request('type') === 'CHEMICAL' ? 'selected' : '' }}>CHEMICAL</option>
            </select>

            <select name="status" class="form-input" style="flex: 1; min-width: 120px;">
                <option value="">All Statuses</option>
                <option value="AVAILABLE" {{ request('status') === 'AVAILABLE' ? 'selected' : '' }}>AVAILABLE</option>
                <option value="ASSIGNED" {{ request('status') === 'ASSIGNED' ? 'selected' : '' }}>ASSIGNED</option>
                <option value="IN_USE" {{ request('status') === 'IN_USE' ? 'selected' : '' }}>IN_USE</option>
                <option value="MAINTENANCE" {{ request('status') === 'MAINTENANCE' ? 'selected' : '' }}>MAINTENANCE</option>
            </select>

            <button type="submit" class="btn-primary" style="width: auto; padding: 10px 24px;">Filter</button>
        </form>

        <a href="/containers/create" class="btn-primary" style="width: auto; padding: 12px 24px; display: inline-flex; align-items: center; gap: 8px;">
            <svg viewBox="0 0 24 24" width="16" height="16" stroke="currentColor" stroke-width="2.5" fill="none" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Add New Container
        </a>
    </div>

    <div class="dashboard-table-card">
        <h2 class="dashboard-table-title">Container Registry</h2>
        <div class="table-responsive">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Container Number</th>
                        <th>Type</th>
                        <th>Status</th>
                        @if(Auth::user()->role === 'ADMIN')
                            <th>Transition Status</th>
                            <th>Actions</th>
                        @else
                            <th>Actions</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($containers as $container)
                        @if(Auth::user()->role === 'ADMIN')
                            <tr data-href="/containers/{{ $container->container_id }}/edit">
                        @else
                            <tr data-href="/containers/{{ $container->container_id }}">
                        @endif
                            <td style="font-weight: 700; color: var(--border-focus);">{{ $container->container_number }}</td>
                            <td style="font-weight: 600;">{{ $container->container_type }}</td>
                            <td>
                                @if($container->status === 'AVAILABLE')
                                    <span class="badge" style="background-color: rgba(16, 185, 129, 0.1); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2);">
                                        AVAILABLE
                                    </span>
                                @elseif($container->status === 'ASSIGNED' || $container->status === 'IN_USE')
                                    <span class="badge" style="background-color: rgba(245, 158, 11, 0.1); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.2);">
                                        {{ $container->status === 'ASSIGNED' ? 'ASSIGNED' : 'IN USE' }}
                                    </span>
                                @elseif($container->status === 'MAINTENANCE')
                                    <span class="badge" style="background-color: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2);">
                                        MAINTENANCE
                                    </span>
                                @endif
                            </td>
                            @if(Auth::user()->role === 'ADMIN')
                                <td>
                                    <form method="POST" action="/containers/{{ $container->container_id }}/status" style="margin: 0; display: flex; gap: 6px; align-items: center;">
                                        @csrf
                                        <select name="status" class="form-input" style="padding: 4px 8px; font-size: 0.8rem; width: auto; height: auto; border-radius: var(--radius-xs); background-color: var(--bg-primary);">
                                            @if($container->status === 'AVAILABLE')
                                                <option value="AVAILABLE" selected>AVAILABLE</option>
                                                <option value="ASSIGNED">ASSIGNED</option>
                                                <option value="MAINTENANCE">MAINTENANCE</option>
                                            @elseif($container->status === 'ASSIGNED' || $container->status === 'IN_USE')
                                                <option value="{{ $container->status }}" selected>{{ $container->status }}</option>
                                                <option value="AVAILABLE">AVAILABLE</option>
                                                <option value="MAINTENANCE">MAINTENANCE</option>
                                            @elseif($container->status === 'MAINTENANCE')
                                                <option value="MAINTENANCE" selected>MAINTENANCE</option>
                                                <option value="AVAILABLE">AVAILABLE</option>
                                                <option value="ASSIGNED">ASSIGNED</option>
                                            @endif
                                        </select>
                                        <button type="submit" class="btn-primary" style="width: auto; padding: 4px 10px; font-size: 0.75rem; border-radius: var(--radius-xs); box-shadow: none;">
                                            Apply
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 8px; align-items: center;">
                                        <a href="/containers/{{ $container->container_id }}/edit" class="btn-secondary" style="width: auto; padding: 6px 14px; font-size: 0.8rem; border-radius: var(--radius-xs); border-color: rgba(255,255,255,0.08);">
                                            Edit
                                        </a>
                                        <form method="POST" action="/containers/{{ $container->container_id }}" style="margin: 0;" onsubmit="return confirm('Are you sure you want to permanently delete container {{ $container->container_number }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-secondary" style="width: auto; padding: 6px 14px; font-size: 0.8rem; border-radius: var(--radius-xs); border-color: rgba(239, 68, 68, 0.6); color: #ef4444; background-color: rgba(239, 68, 68, 0.05);">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            @else
                                <td>
                                    <a href="/containers/{{ $container->container_id }}" class="btn-secondary" style="width: auto; padding: 6px 14px; font-size: 0.8rem; border-radius: var(--radius-xs); border-color: rgba(255,255,255,0.08);">
                                        View Details
                                    </a>
                                </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ Auth::user()->role === 'ADMIN' ? 5 : 4 }}" style="text-align: center; padding: 32px; color: var(--text-secondary);">
                                No containers found matching your criteria.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>v>

</div>

@endsection
