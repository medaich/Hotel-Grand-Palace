@extends('layouts.app')

@section('title', 'Activity Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold"><i class="fas fa-history me-2"></i>Activity Logs</h5>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Time</th>
                    <th>User</th>
                    <th>Action</th>
                    <th>IP Address</th>
                    <th>User Agent</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td class="text-nowrap">{{ $log->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>
                        @if($log->user)
                            <strong>{{ $log->user->username }}</strong><br>
                            <small class="text-muted">{{ ucfirst($log->user->role) }}</small>
                        @else
                            <span class="text-muted">System/Unknown</span>
                        @endif
                    </td>
                    <td>{{ $log->action }}</td>
                    <td><code>{{ $log->ip_address }}</code></td>
                    <td><small class="text-muted" title="{{ $log->user_agent }}">{{ Str::limit($log->user_agent, 40) }}</small></td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">No activity logs found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($logs->hasPages())
    <div class="card-footer bg-white border-top-0 pt-3">
        {{ $logs->links() }}
    </div>
    @endif
</div>
@endsection
