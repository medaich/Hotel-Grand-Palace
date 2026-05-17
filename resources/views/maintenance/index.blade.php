@extends('layouts.app')

@section('title', 'Maintenance')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-tools text-primary me-2"></i>Maintenance</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addMaintenanceModal">
        <i class="fas fa-plus-circle me-1"></i> Report Issue
    </button>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Room</th>
                    <th>Issue</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Reported By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($maintenances as $maintenance)
                <tr>
                    <td>Rm {{ optional($maintenance->room)->room_number }}</td>
                    <td>{{ Str::limit($maintenance->issue, 50) }}</td>
                    <td>
                        <span class="badge bg-{{ $maintenance->priority === 'urgent' ? 'danger' : ($maintenance->priority === 'high' ? 'warning' : 'info') }}">
                            {{ ucfirst($maintenance->priority) }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-{{ $maintenance->status === 'resolved' ? 'success' : ($maintenance->status === 'cancelled' ? 'secondary' : 'primary') }}">
                            {{ ucfirst(str_replace('_', ' ', $maintenance->status)) }}
                        </span>
                    </td>
                    <td>{{ optional($maintenance->reportedBy)->full_name ?? 'System' }}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('maintenance.index', ['edit' => $maintenance->id]) }}" class="btn btn-outline-secondary"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('maintenance.destroy', $maintenance) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this request?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-muted">No maintenance requests found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addMaintenanceModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('maintenance.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Report Issue</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label>Room</label>
                    <select name="room_id" class="form-select" required>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}">{{ $room->room_number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12"><label>Issue Description</label><textarea name="issue" class="form-control" rows="3" required></textarea></div>
                <div class="col-6">
                    <label>Priority</label>
                    <select name="priority" class="form-select" required>
                        <option value="low">Low</option>
                        <option value="normal" selected>Normal</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>
                <div class="col-6">
                    <label>Status</label>
                    <select name="status" class="form-select" required>
                        <option value="open">Open</option>
                        <option value="in_progress">In Progress</option>
                        <option value="resolved">Resolved</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Submit Report</button>
            </div>
        </form>
    </div>
</div>

@if($edit_maintenance)
<!-- Edit Modal -->
<div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('maintenance.update', $edit_maintenance) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">Edit Request</h5>
                <a href="{{ route('maintenance.index') }}" class="btn-close"></a>
            </div>
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label>Room</label>
                    <select name="room_id" class="form-select" required>
                        @foreach($rooms as $room)
                            <option value="{{ $room->id }}" {{ $edit_maintenance->room_id == $room->id ? 'selected' : '' }}>{{ $room->room_number }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12"><label>Issue</label><textarea name="issue" class="form-control" rows="3" required>{{ $edit_maintenance->issue }}</textarea></div>
                <div class="col-6">
                    <label>Priority</label>
                    <select name="priority" class="form-select" required>
                        <option value="low" {{ $edit_maintenance->priority == 'low' ? 'selected' : '' }}>Low</option>
                        <option value="normal" {{ $edit_maintenance->priority == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="high" {{ $edit_maintenance->priority == 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ $edit_maintenance->priority == 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>
                <div class="col-6">
                    <label>Status</label>
                    <select name="status" class="form-select" required>
                        <option value="open" {{ $edit_maintenance->status == 'open' ? 'selected' : '' }}>Open</option>
                        <option value="in_progress" {{ $edit_maintenance->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                        <option value="resolved" {{ $edit_maintenance->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                        <option value="cancelled" {{ $edit_maintenance->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary">Update Report</button>
            </div>
        </form>
    </div>
</div>
@endif

@endsection
