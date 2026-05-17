@extends('layouts.app')

@section('title', 'Reports Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="mb-0"><i class="fas fa-chart-line text-primary me-2"></i>Reports</h2>
</div>

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body bg-light rounded">
        <form method="GET" action="{{ route('reports.index') }}" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
            </div>
            <div class="col-md-4">
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-filter me-2"></i>Generate Report</button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-6 col-lg-3">
        <div class="card text-center shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="display-6 text-success mb-2"><i class="fas fa-dollar-sign"></i></div>
                <h5 class="text-muted">Total Revenue</h5>
                <h3>${{ number_format($totalRevenue, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card text-center shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="display-6 text-info mb-2"><i class="fas fa-concierge-bell"></i></div>
                <h5 class="text-muted">Services Revenue</h5>
                <h3>${{ number_format($servicesRevenue, 2) }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card text-center shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="display-6 text-primary mb-2"><i class="fas fa-calendar-check"></i></div>
                <h5 class="text-muted">Total Bookings</h5>
                <h3>{{ $totalBookings }}</h3>
            </div>
        </div>
    </div>
    <div class="col-md-6 col-lg-3">
        <div class="card text-center shadow-sm border-0 h-100">
            <div class="card-body">
                <div class="display-6 text-danger mb-2"><i class="fas fa-ban"></i></div>
                <h5 class="text-muted">Cancellations</h5>
                <h3>{{ $cancelledBookings }}</h3>
            </div>
        </div>
    </div>
</div>

@endsection
