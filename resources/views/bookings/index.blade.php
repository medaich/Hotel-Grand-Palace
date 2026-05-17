@extends('layouts.app')

@section('title', 'Bookings')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold"><i class="fas fa-calendar-check me-2"></i>Bookings</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newBookingModal">
        <i class="fas fa-plus me-2"></i>New Booking
    </button>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {!! session('success') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if (request()->has('updated'))
    <div class="alert alert-success">Booking updated.</div>
@endif
@if (request()->has('deleted'))
    <div class="alert alert-warning">Booking deleted.</div>
@endif

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <form method="GET" class="row g-2">
            <div class="col-sm-3">
                <input type="text" name="search" class="form-control" placeholder="Search guest / booking ref"
                       value="{{ request('search') }}">
            </div>
            <div class="col-sm-2">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['pending','confirmed','checked_in','checked_out','cancelled'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <input type="date" name="date" class="form-control" value="{{ request('date') }}">
            </div>
            <div class="col-sm-2">
                <select name="sort" class="form-select">
                    <option value="created_at"  {{ request('sort') === 'created_at' ? 'selected' : '' }}>Sort: Created</option>
                    <option value="check_in"    {{ request('sort') === 'check_in' ? 'selected' : '' }}>Sort: Check-in</option>
                    <option value="total_price" {{ request('sort') === 'total_price' ? 'selected' : '' }}>Sort: Price</option>
                </select>
            </div>
            <div class="col-sm-1">
                <select name="order" class="form-select">
                    <option value="desc" {{ request('order') === 'desc' ? 'selected' : '' }}>DESC</option>
                    <option value="asc"  {{ request('order') === 'asc' ? 'selected' : '' }}>ASC</option>
                </select>
            </div>
            <div class="col-sm-2">
                <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>Ref</th>
                    <th>Guest</th>
                    <th>Room</th>
                    <th>Check-in</th>
                    <th>Check-out</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Payment</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                <tr>
                    <td><strong>{{ $booking->booking_ref }}</strong></td>
                    <td>{{ optional($booking->guest)->first_name }} {{ optional($booking->guest)->last_name }}<br><small class="text-muted">{{ optional($booking->guest)->email }}</small></td>
                    <td>{{ optional($booking->room)->room_number }} <small class="text-muted">({{ optional($booking->room)->room_type }})</small></td>
                    <td>{{ $booking->check_in }}</td>
                    <td>{{ $booking->check_out }}</td>
                    <td><strong>${{ number_format($booking->total_price, 2) }}</strong></td>
                    <td>
                        @php
                            $statusColors = ['pending'=>'warning','confirmed'=>'primary','checked_in'=>'success','checked_out'=>'secondary','cancelled'=>'danger'];
                            $color = $statusColors[$booking->status] ?? 'dark';
                        @endphp
                        <span class="badge bg-{{ $color }}">{{ ucfirst(str_replace('_',' ',$booking->status)) }}</span>
                    </td>
                    <td>
                        @php
                            $paymentColors = ['paid'=>'success','unpaid'=>'danger','partial'=>'warning'];
                            $pColor = $paymentColors[$booking->payment_status] ?? 'secondary';
                        @endphp
                        <span class="badge bg-{{ $pColor }}">{{ ucfirst($booking->payment_status) }}</span>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('bookings.index', array_merge(request()->query(), ['view' => $booking->id])) }}" class="btn btn-outline-info"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('bookings.index', array_merge(request()->query(), ['edit' => $booking->id])) }}" class="btn btn-outline-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('bookings.destroy', $booking) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this booking?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="9" class="text-center py-4 text-muted">No bookings found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($view_booking)
<div class="card mt-4 shadow-sm border-0">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Booking Details — {{ $view_booking->booking_ref }}</h5>
            <a href="{{ route('bookings.index', request()->except('view')) }}" class="btn-close"></a>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-bordered">
                    <tr><th class="bg-light" width="30%">Guest</th><td>{{ optional($view_booking->guest)->first_name }} {{ optional($view_booking->guest)->last_name }}</td></tr>
                    <tr><th class="bg-light">Email</th><td>{{ optional($view_booking->guest)->email }}</td></tr>
                    <tr><th class="bg-light">Room</th><td>{{ optional($view_booking->room)->room_number }} ({{ ucfirst(optional($view_booking->room)->room_type) }})</td></tr>
                    <tr><th class="bg-light">Check-in</th><td>{{ $view_booking->check_in }}</td></tr>
                    <tr><th class="bg-light">Check-out</th><td>{{ $view_booking->check_out }}</td></tr>
                    <tr><th class="bg-light">Adults</th><td>{{ $view_booking->adults }}</td></tr>
                    <tr><th class="bg-light">Children</th><td>{{ $view_booking->children }}</td></tr>
                    <tr><th class="bg-light">Total</th><td><strong>${{ number_format($view_booking->total_price, 2) }}</strong></td></tr>
                    <tr><th class="bg-light">Payment Method</th><td>{{ ucfirst(str_replace('_', ' ', $view_booking->payment_method)) }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold">Special Requests</h6>
                <p class="p-3 bg-light rounded">{{ $view_booking->special_requests ?? 'None' }}</p>
            </div>
        </div>
    </div>
</div>
@endif

@if($edit_booking)
<div class="card mt-4 shadow-sm border-0 border-top border-warning border-3">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold">Update Booking</h5>
            <a href="{{ route('bookings.index', request()->except('edit')) }}" class="btn-close"></a>
        </div>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('bookings.update', $edit_booking) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Booking Status</label>
                    <select name="status" class="form-select">
                        @foreach(['pending','confirmed','checked_in','checked_out','cancelled'] as $s)
                        <option value="{{ $s }}" {{ $edit_booking->status === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ',$s)) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Payment Status</label>
                    <select name="payment_status" class="form-select">
                        @foreach(['unpaid','partial','paid'] as $p)
                        <option value="{{ $p }}" {{ $edit_booking->payment_status === $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('bookings.index', request()->except('edit')) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endif

<!-- New Booking Modal -->
<div class="modal fade" id="newBookingModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <form class="modal-content" action="{{ route('bookings.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">New Booking</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Guest *</label>
                        <select name="guest_id" class="form-select" required>
                            <option value="">Select Guest</option>
                            @foreach($guests as $guest)
                            <option value="{{ $guest->id }}">{{ $guest->first_name }} {{ $guest->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Room *</label>
                        <select name="room_id" class="form-select" required id="roomSelect">
                            <option value="">Select Room</option>
                            @foreach($rooms as $room)
                            <option value="{{ $room->id }}" data-price="{{ $room->price_per_night }}">
                                {{ $room->room_number }} - {{ ucfirst($room->room_type) }} (${{ $room->price_per_night }}/night)
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Check-in *</label>
                        <input type="date" name="check_in" class="form-control" id="checkIn" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Check-out *</label>
                        <input type="date" name="check_out" class="form-control" id="checkOut" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Adults</label>
                        <input type="number" name="adults" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Children</label>
                        <input type="number" name="children" class="form-control" value="0" min="0">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Total ($)</label>
                        <input type="number" step="0.01" name="total_price" class="form-control" id="totalPrice" value="0">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Payment Method</label>
                        <select name="payment_method" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="online">Online Payment</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Special Requests</label>
                        <textarea name="special_requests" class="form-control" rows="3"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-calendar-plus me-2"></i>Create Booking</button>
            </div>
        </form>
    </div>
</div>

<script>
function calcPrice() {
    var room = document.getElementById('roomSelect');
    if (!room.options[room.selectedIndex]) return;
    var rate = parseFloat(room.options[room.selectedIndex].getAttribute('data-price')) || 0;
    var cinStr = document.getElementById('checkIn').value;
    var coutStr = document.getElementById('checkOut').value;
    if(!cinStr || !coutStr) return;
    var cin  = new Date(cinStr);
    var cout = new Date(coutStr);
    var days = Math.max(0, (cout - cin) / (1000*60*60*24));
    document.getElementById('totalPrice').value = (rate * days).toFixed(2);
}
document.getElementById('roomSelect').addEventListener('change', calcPrice);
document.getElementById('checkIn').addEventListener('change', calcPrice);
document.getElementById('checkOut').addEventListener('change', calcPrice);
</script>
@endsection
