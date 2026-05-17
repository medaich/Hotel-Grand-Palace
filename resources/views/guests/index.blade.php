@extends('layouts.app')

@section('title', 'Guest Management')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold"><i class="fas fa-users me-2"></i>Guest Management</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addGuestModal">
        <i class="fas fa-user-plus me-2"></i>Register Guest
    </button>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        {!! session('success') !!}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if (request()->has('deleted'))
    <div class="alert alert-warning">Guest record deleted.</div>
@endif

<div class="card mb-4 shadow-sm border-0">
    <div class="card-body">
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="q" class="form-control" placeholder="Search by name, email, ID number, nationality..."
                   value="{{ request('q') }}">
            <button type="submit" class="btn btn-primary px-4"><i class="fas fa-search"></i></button>
        </form>
        @if(request('q'))
        <small class="text-muted mt-2 d-block">Showing results for: {{ request('q') }}</small>
        @endif
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-0 table-responsive">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Nationality</th>
                    <th>ID Number</th>
                    <th>Notes</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($guests as $guest)
                <tr>
                    <td>{{ $guest->id }}</td>
                    <td>{{ $guest->first_name }} {{ $guest->last_name }}</td>
                    <td>{{ $guest->email }}</td>
                    <td>{{ $guest->phone }}</td>
                    <td>{{ $guest->nationality }}</td>
                    <td>{{ $guest->id_number }}</td>
                    <td>{{ Str::limit($guest->notes, 30) }}</td>
                    <td>
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('guests.index', ['view' => $guest->id]) }}" class="btn btn-outline-info"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('guests.index', ['edit' => $guest->id]) }}" class="btn btn-outline-warning"><i class="fas fa-edit"></i></a>
                            <form action="{{ route('guests.destroy', $guest) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this guest?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="text-center py-4 text-muted">No guests found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@if($view_guest)
<div class="card mt-4 shadow-sm border-0">
    <div class="card-header bg-white fw-bold">Guest Profile — {{ $view_guest->first_name }} {{ $view_guest->last_name }}</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm table-bordered">
                    <tr><th class="bg-light" width="30%">Full Name</th><td>{{ $view_guest->first_name }} {{ $view_guest->last_name }}</td></tr>
                    <tr><th class="bg-light">Email</th><td>{{ $view_guest->email }}</td></tr>
                    <tr><th class="bg-light">Phone</th><td>{{ $view_guest->phone }}</td></tr>
                    <tr><th class="bg-light">ID Number</th><td>{{ $view_guest->id_number }}</td></tr>
                    <tr><th class="bg-light">Nationality</th><td>{{ $view_guest->nationality }}</td></tr>
                    <tr><th class="bg-light">Date of Birth</th><td>{{ $view_guest->date_of_birth }}</td></tr>
                    <tr><th class="bg-light">Address</th><td>{{ $view_guest->address }}</td></tr>
                    <tr><th class="bg-light">Notes</th><td>{{ $view_guest->notes }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="fw-bold">Booking History</h6>
                <table class="table table-sm table-striped">
                    <thead class="table-light"><tr><th>Ref</th><th>Room</th><th>Check-in</th><th>Status</th></tr></thead>
                    <tbody>
                    @forelse($view_guest->bookings as $bk)
                    <tr>
                        <td>{{ $bk->booking_ref }}</td>
                        <td>{{ optional($bk->room)->room_number }}</td>
                        <td>{{ $bk->check_in }}</td>
                        <td><span class="badge bg-secondary">{{ $bk->status }}</span></td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center py-2 text-muted">No bookings found.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif

@if($edit_guest)
<div class="card mt-4 shadow-sm border-0 border-top border-warning border-3">
    <div class="card-header bg-white fw-bold">Edit Guest</div>
    <div class="card-body">
        <form method="POST" action="{{ route('guests.update', $edit_guest) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">First Name</label>
                    <input type="text" name="first_name" class="form-control" value="{{ $edit_guest->first_name }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Last Name</label>
                    <input type="text" name="last_name" class="form-control" value="{{ $edit_guest->last_name }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ $edit_guest->email }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" value="{{ $edit_guest->phone }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ID Number</label>
                    <input type="text" name="id_number" class="form-control" value="{{ $edit_guest->id_number }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Nationality</label>
                    <input type="text" name="nationality" class="form-control" value="{{ $edit_guest->nationality }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Address</label>
                    <textarea name="address" class="form-control" rows="2">{{ $edit_guest->address }}</textarea>
                </div>
                <div class="col-12">
                    <label class="form-label">Notes</label>
                    <textarea name="notes" class="form-control" rows="2">{{ $edit_guest->notes }}</textarea>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('guests.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Add Guest Modal -->
<div class="modal fade" id="addGuestModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form class="modal-content" action="{{ route('guests.store') }}" method="POST">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Register New Guest</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body row g-3">
                <div class="col-md-6"><label class="form-label">First Name *</label><input type="text" name="first_name" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Last Name *</label><input type="text" name="last_name" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Phone</label><input type="text" name="phone" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">ID / Passport Number</label><input type="text" name="id_number" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Nationality</label><input type="text" name="nationality" class="form-control"></div>
                <div class="col-md-6"><label class="form-label">Date of Birth</label><input type="date" name="date_of_birth" class="form-control"></div>
                <div class="col-12"><label class="form-label">Address</label><textarea name="address" class="form-control" rows="2"></textarea></div>
                <div class="col-12"><label class="form-label">Notes (internal)</label><textarea name="notes" class="form-control" rows="2"></textarea></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-user-plus me-2"></i>Register Guest</button>
            </div>
        </form>
    </div>
</div>

@endsection
