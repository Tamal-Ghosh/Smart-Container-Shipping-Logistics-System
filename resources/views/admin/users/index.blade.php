@extends('layouts.app')

@section('title', 'User Accounts Registry — Smart Shipping')

@section('content')

<div class="dashboard-wrapper">

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 24px;">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="margin-bottom: 24px;">{{ session('error') }}</div>
    @endif

    <!-- Filtering panel -->
    <div class="dashboard-table-card" style="padding: 24px; margin-bottom: 32px;">
        <h3 class="dashboard-table-title" style="font-size: 1rem; margin-bottom: 16px;">🔍 Filter & Search Users</h3>
        <form method="GET" action="/admin/users" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; align-items: end;">
            
            <div>
                <label class="form-label" style="font-size: 0.725rem;">Search Username/Email</label>
                <input type="text" name="search" placeholder="Enter username or email..." value="{{ request('search') }}" class="form-input">
            </div>

            <div>
                <label class="form-label" style="font-size: 0.725rem;">Role</label>
                <select name="role" class="form-input" style="background-color: var(--bg-primary);">
                    <option value="">All Roles</option>
                    <option value="ADMIN" {{ request('role') === 'ADMIN' ? 'selected' : '' }}>ADMIN</option>
                    <option value="OPERATOR" {{ request('role') === 'OPERATOR' ? 'selected' : '' }}>OPERATOR</option>
                    <option value="CUSTOMER" {{ request('role') === 'CUSTOMER' ? 'selected' : '' }}>CUSTOMER</option>
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: 0.725rem;">Status</label>
                <select name="status" class="form-input" style="background-color: var(--bg-primary);">
                    <option value="">All Statuses</option>
                    <option value="Y" {{ request('status') === 'Y' ? 'selected' : '' }}>ACTIVE</option>
                    <option value="N" {{ request('status') === 'N' ? 'selected' : '' }}>DEACTIVATED</option>
                </select>
            </div>

            <div style="grid-column: span 1; display: flex; gap: 8px;">
                <a href="/admin/users" class="btn-secondary" style="flex: 1; padding: 10px; font-size: 0.85rem;">Reset</a>
                <button type="submit" class="btn-primary" style="flex: 2; padding: 10px; font-size: 0.85rem;">Filter</button>
            </div>

        </form>
    </div>

    <!-- Users Registry Table -->
    <div class="dashboard-table-card">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 16px; margin-bottom: 20px;">
            <h2 class="dashboard-table-title" style="margin-bottom: 0;">👥 User Accounts Registry</h2>
            <a href="/admin/users/create" class="btn-primary" style="width: auto; padding: 10px 20px; font-size: 0.85rem;">
                ➕ Create Operator
            </a>
        </div>

        <div class="table-responsive">
            <table class="dashboard-table">
                <thead>
                    <tr>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered Date</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr style="{{ $user->is_active === 'N' ? 'opacity: 0.65; background-color: rgba(239, 68, 68, 0.02);' : '' }}">
                            <td style="font-weight: 700;">{{ $user->username }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge" style="
                                    @if($user->role === 'ADMIN')
                                        background-color: rgba(139, 92, 246, 0.1); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.2);
                                    @elseif($user->role === 'OPERATOR')
                                        background-color: rgba(59, 130, 246, 0.1); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2);
                                    @else
                                        background-color: rgba(16, 185, 129, 0.1); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2);
                                    @endif
                                ">
                                    {{ $user->role }}
                                </span>
                            </td>
                            <td>
                                {{ $user->created_at ? \Carbon\Carbon::parse($user->created_at)->format('M d, Y') : 'N/A' }}
                            </td>
                            <td>
                                @if($user->is_active === 'Y')
                                    <span class="badge badge-active" style="background-color: rgba(16, 185, 129, 0.1); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2);">ACTIVE</span>
                                @else
                                    <span class="badge badge-maintenance" style="background-color: rgba(239, 68, 68, 0.1); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2);">DEACTIVATED</span>
                                @endif
                            </td>
                            <td>
                                <div style="display: flex; gap: 8px; align-items: center;">
                                    @if(Auth::user()->user_id != $user->user_id)
                                        <form method="POST" action="/admin/users/{{ $user->user_id }}/toggle" style="margin: 0;">
                                            @csrf
                                            @if($user->is_active === 'Y')
                                                <button type="submit" class="btn-secondary" style="width: auto; padding: 6px 14px; font-size: 0.8rem; border-color: rgba(239, 68, 68, 0.4); color: #f87171;" onclick="return confirm('Are you sure you want to deactivate user {{ $user->username }}?')">
                                                    Deactivate
                                                </button>
                                            @else
                                                <button type="submit" class="btn-primary" style="width: auto; padding: 6px 14px; font-size: 0.8rem; background-color: rgba(16, 185, 129, 0.2); border-color: #34d399; color: #34d399;">
                                                    Activate
                                                </button>
                                            @endif
                                        </form>

                                        <form method="POST" action="/admin/users/{{ $user->user_id }}" style="margin: 0;" onsubmit="return confirm('Are you sure you want to permanently delete user {{ $user->username }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-secondary" style="width: auto; padding: 6px 14px; font-size: 0.8rem; border-color: rgba(239, 68, 68, 0.6); color: #ef4444; background-color: rgba(239, 68, 68, 0.05);">
                                                Delete
                                            </button>
                                        </form>
                                    @else
                                        <span style="font-size: 0.8rem; color: var(--text-muted); font-style: italic;">Self (Locked)</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                                No user accounts found matching the query.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

@endsection
