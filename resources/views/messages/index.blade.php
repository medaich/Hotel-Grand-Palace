@extends('layouts.app')

@section('title', 'Internal Messages')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold"><i class="fas fa-envelope me-2"></i>Internal Messages</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#composeModal">
        <i class="fas fa-pen me-2"></i>Compose
    </button>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {!! session('success') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>From</th>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($messages as $msg)
                <tr class="{{ $msg->is_read ? '' : 'fw-bold bg-light' }}">
                    <td>{{ optional($msg->sender)->username ?? 'System' }}</td>
                    <td>
                        <a href="{{ route('messages.index', ['view' => $msg->id]) }}" class="text-decoration-none">
                            {{ $msg->subject }}
                        </a>
                    </td>
                    <td>{{ $msg->created_at->format('Y-m-d H:i:s') }}</td>
                    <td>
                        @if($msg->is_read)
                            <span class="text-muted">Read</span>
                        @else
                            <span class="badge bg-primary">New</span>
                        @endif
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('messages.index', ['view' => $msg->id]) }}" class="btn btn-outline-info"><i class="fas fa-eye"></i></a>
                            <form action="{{ route('messages.destroy', $msg) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete message?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-4 text-muted">No messages found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($view_message)
<div class="card mt-4 shadow-sm border-0">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ $view_message->subject }}</strong>
                <span class="text-muted ms-3">From: {{ optional($view_message->sender)->username ?? 'System' }}</span>
                <span class="text-muted ms-2">{{ $view_message->created_at->format('Y-m-d H:i:s') }}</span>
            </div>
            <a href="{{ route('messages.index') }}" class="btn-close"></a>
        </div>
    </div>
    <div class="card-body">
        {!! nl2br(e($view_message->body)) !!}
    </div>
</div>
@endif

<!-- Compose Modal -->
<div class="modal fade" id="composeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" action="{{ route('messages.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Compose Message</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Subject</label>
                    <input type="text" name="subject" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Message</label>
                    <textarea name="body" class="form-control" rows="6" placeholder="HTML is supported..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i>Send</button>
            </div>
        </form>
    </div>
</div>

@endsection
