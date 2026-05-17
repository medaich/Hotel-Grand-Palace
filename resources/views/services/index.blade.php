@extends('layouts.app')

@section('title', 'Services Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-concierge-bell text-primary me-2"></i>Services</h2>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
        <i class="fas fa-plus-circle me-1"></i> Add Service
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
                    <th>Service Name</th>
                    <th>Booking Ref</th>
                    <th>Guest</th>
                    <th>Qty</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                <tr>
                    <td>{{ $service->service_name }}</td>
                    <td>{{ $service->booking->booking_ref }}</td>
                    <td>{{ optional($service->booking->guest)->first_name }} {{ optional($service->booking->guest)->last_name }}</td>
                    <td>{{ $service->quantity }}</td>
                    <td>${{ number_format($service->unit_price, 2) }}</td>
                    <td>${{ number_format($service->total, 2) }}</td>
                    <td>
                        <form action="{{ route('services.destroy', $service) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this service?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">No services found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Service Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" action="{{ route('services.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">New Service Charge</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-12">
                    <label>Booking</label>
                    <select name="booking_id" class="form-select" required>
                        @foreach($bookings as $booking)
                            <option value="{{ $booking->id }}">{{ $booking->booking_ref }} - {{ optional($booking->guest)->first_name }} (Rm {{ optional($booking->room)->room_number }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12"><label>Service Name</label><input type="text" name="service_name" class="form-control" required></div>
                <div class="col-6"><label>Quantity</label><input type="number" name="quantity" class="form-control" value="1" min="1" required></div>
                <div class="col-6"><label>Unit Price ($)</label><input type="number" name="unit_price" step="0.01" class="form-control" required></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Save Service</button>
            </div>
        </form>
    </div>
</div>

@endsection
