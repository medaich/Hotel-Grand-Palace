@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
@if ($search)
<div class="alert alert-info">
    <i class="fas fa-search me-2"></i>Search results for: <strong>{{ $search }}</strong>
</div>
@endif

<!-- Stats Row -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#0f3460,#533483)">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="text-white-50 small mb-1">Total Rooms</div><h3 class="mb-0">{{ $total_rooms }}</h3></div>
                <i class="fas fa-door-open fa-2x opacity-50"></i>
            </div>
            <div class="mt-2 small text-white-50">
                {{ $available_rooms }} available &bull; {{ $occupied_rooms }} occupied
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#198754,#20c997)">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="text-white-50 small mb-1">Total Guests</div><h3 class="mb-0">{{ $total_guests }}</h3></div>
                <i class="fas fa-users fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#0dcaf0,#0d6efd)">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="text-white-50 small mb-1">Bookings</div><h3 class="mb-0">{{ $total_bookings }}</h3></div>
                <i class="fas fa-calendar-check fa-2x opacity-50"></i>
            </div>
            <div class="mt-2 small text-white-50">{{ $pending_bookings }} pending</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card" style="background:linear-gradient(135deg,#e2b96f,#dc3545)">
            <div class="d-flex justify-content-between align-items-center">
                <div><div class="text-white-50 small mb-1">Revenue</div><h3 class="mb-0">${{ number_format($total_revenue, 2) }}</h3></div>
                <i class="fas fa-dollar-sign fa-2x opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<!-- Search Bar -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('dashboard') }}" class="d-flex gap-2">
            <input type="text" name="search" class="form-control" placeholder="Search bookings by guest name or booking ref..."
                   value="{{ $search ?? '' }}">
            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-search"></i></button>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">Clear</a>
        </form>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Bookings -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span><i class="fas fa-calendar-check me-2"></i>Recent Bookings</span>
                <a href="{{ route('bookings.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr>
                        <th>Ref</th><th>Guest</th><th>Room</th>
                        <th>Check-in</th><th>Check-out</th><th>Status</th><th>Action</th>
                    </tr></thead>
                    <tbody>
                    @foreach ($recent_bookings as $b)
                    <tr>
                        <td><a href="#">{{ $b->booking_ref }}</a></td>
                        <td>{{ $b->guest->first_name }} {{ $b->guest->last_name }}</td>
                        <td>{{ $b->room->room_number }}</td>
                        <td>{{ $b->check_in }}</td>
                        <td>{{ $b->check_out }}</td>
                        <td>
                            @php
                            $sc = ['pending'=>'warning','confirmed'=>'primary','checked_in'=>'success','checked_out'=>'secondary','cancelled'=>'danger'];
                            $s  = $b->status;
                            @endphp
                            <span class="badge bg-{{ $sc[$s] ?? 'dark' }}">{{ ucfirst(str_replace('_',' ',$s)) }}</span>
                        </td>
                        <td>
                            <a href="#" class="btn btn-sm btn-outline-primary">View</a>
                        </td>
                    </tr>
                    @endforeach
                    @if ($recent_bookings->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No bookings found.</td>
                    </tr>
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Today panel -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><i class="fas fa-sign-in-alt me-2 text-success"></i>Today's Check-ins</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                @forelse ($todays_checkins as $ci)
                    <li class="list-group-item d-flex justify-content-between small py-2">
                        <span>{{ $ci->guest->first_name }} {{ $ci->guest->last_name }}</span>
                        <span class="text-muted">Rm {{ $ci->room->room_number }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted small py-2">No check-ins today</li>
                @endforelse
                </ul>
            </div>
        </div>

        <div class="card">
            <div class="card-header"><i class="fas fa-sign-out-alt me-2 text-danger"></i>Today's Check-outs</div>
            <div class="card-body p-0">
                <ul class="list-group list-group-flush">
                @forelse ($todays_checkouts as $co)
                    <li class="list-group-item d-flex justify-content-between small py-2">
                        <span>{{ $co->guest->first_name }} {{ $co->guest->last_name }}</span>
                        <span class="text-muted">Rm {{ $co->room->room_number }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted small py-2">No check-outs today</li>
                @endforelse
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
