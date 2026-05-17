@extends('layouts.app')

@section('title', 'Rooms Management')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0 fw-bold"><i class="fas fa-door-open me-2"></i>Rooms Management</h5>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRoomModal">
        <i class="fas fa-plus me-2"></i>Add Room
    </button>
</div>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('rooms.index') }}" class="row g-2">
            <div class="col-sm-3">
                <select name="type" class="form-select">
                    <option value="">All Types</option>
                    @foreach(['single','double','suite','penthouse'] as $t)
                    <option value="{{ $t }}" {{ request('type') === $t ? 'selected' : '' }}>{{ ucfirst($t) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-3">
                <select name="status" class="form-select">
                    <option value="">All Statuses</option>
                    @foreach(['available','occupied','maintenance'] as $s)
                    <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-sm-2">
                <input type="number" name="floor" class="form-control" placeholder="Floor"
                       value="{{ request('floor') }}">
            </div>
            <div class="col-sm-2">
                <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
            </div>
            <div class="col-sm-2">
                <a href="{{ route('rooms.index') }}" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
        </form>
    </div>
</div>

<!-- Rooms Table -->
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead><tr>
                <th>Room #</th><th>Type</th><th>Floor</th><th>Capacity</th>
                <th>Price/Night</th><th>Status</th><th>Description</th><th>Actions</th>
            </tr></thead>
            <tbody>
            @foreach ($rooms as $room)
            <tr>
                <td><strong>{{ $room->room_number }}</strong></td>
                <td>{{ ucfirst($room->room_type) }}</td>
                <td>{{ $room->floor }}</td>
                <td>{{ $room->capacity }}</td>
                <td>${{ number_format($room->price_per_night, 2) }}</td>
                <td>
                    <span class="status-{{ $room->status }}">
                        {{ ucfirst($room->status) }}
                    </span>
                </td>
                <td>{{ $room->description }}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('rooms.index', ['view' => $room->id]) }}" class="btn btn-outline-info">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('rooms.index', ['edit' => $room->id]) }}" class="btn btn-outline-warning">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form method="POST" action="{{ route('rooms.destroy', $room->id) }}" style="display:inline;" onsubmit="return confirm('Delete room?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger" style="border-top-left-radius: 0; border-bottom-left-radius: 0;">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                    <div class="mt-1">
                        <form method="POST" action="{{ route('rooms.status', $room->id) }}">
                            @csrf
                            @method('PATCH')
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                @foreach(['available','occupied','maintenance'] as $s)
                                <option value="{{ $s }}" {{ $room->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@if ($view_room)
<div class="card mt-4">
    <div class="card-header">Room Details — #{{ $view_room->room_number }}</div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-sm">
                    <tr><th>Type</th><td>{{ $view_room->room_type }}</td></tr>
                    <tr><th>Floor</th><td>{{ $view_room->floor }}</td></tr>
                    <tr><th>Capacity</th><td>{{ $view_room->capacity }}</td></tr>
                    <tr><th>Price/Night</th><td>${{ $view_room->price_per_night }}</td></tr>
                    <tr><th>Status</th><td>{{ $view_room->status }}</td></tr>
                    <tr><th>Amenities</th><td>{{ $view_room->amenities }}</td></tr>
                </table>
            </div>
            <div class="col-md-6">
                <p>{{ $view_room->description }}</p>
                @if ($view_room->image_path)
                <img src="{{ $view_room->image_path }}" class="img-fluid rounded" style="max-height:200px">
                @endif
            </div>
        </div>
    </div>
</div>
@endif

@if ($edit_room)
<div class="card mt-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="fas fa-edit me-2"></i>Edit Room #{{ $edit_room->room_number }}</span>
        <a href="{{ route('rooms.index') }}" class="btn btn-sm btn-outline-secondary">Cancel</a>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('rooms.update', $edit_room->id) }}">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Room Number *</label>
                    <input type="text" name="room_number" class="form-control"
                           value="{{ $edit_room->room_number }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Type *</label>
                    <select name="room_type" class="form-select">
                        @foreach(['single','double','suite','penthouse'] as $t)
                        <option value="{{ $t }}" {{ $edit_room->room_type===$t?'selected':'' }}>
                            {{ ucfirst($t) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Floor</label>
                    <input type="number" name="floor" class="form-control"
                           value="{{ $edit_room->floor }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Capacity</label>
                    <input type="number" name="capacity" class="form-control"
                           value="{{ $edit_room->capacity }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Price/Night ($)</label>
                    <input type="number" step="0.01" name="price_per_night" class="form-control"
                           value="{{ $edit_room->price_per_night }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        @foreach(['available','occupied','maintenance'] as $s)
                        <option value="{{ $s }}" {{ $edit_room->status===$s?'selected':'' }}>
                            {{ ucfirst($s) }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-8">
                    <label class="form-label">Amenities</label>
                    <input type="text" name="amenities" class="form-control"
                           value="{{ $edit_room->amenities }}">
                </div>
                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="3">{{ $edit_room->description }}</textarea>
                </div>
            </div>
            <div class="mt-3">
                <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Save Changes</button>
                <a href="{{ route('rooms.index') }}" class="btn btn-secondary ms-2">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endif

<!-- Add Room Modal -->
<div class="modal fade" id="addRoomModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Room</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('rooms.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Room Number *</label>
                            <input type="text" name="room_number" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Type *</label>
                            <select name="room_type" class="form-select">
                                <option value="single">Single</option>
                                <option value="double">Double</option>
                                <option value="suite">Suite</option>
                                <option value="penthouse">Penthouse</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Floor</label>
                            <input type="number" name="floor" class="form-control" value="1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Capacity</label>
                            <input type="number" name="capacity" class="form-control" value="2">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Price/Night ($) *</label>
                            <input type="number" step="0.01" name="price_per_night" class="form-control" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Amenities</label>
                            <input type="text" name="amenities" class="form-control" placeholder="WiFi, TV, AC, Mini-bar">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Room Image</label>
                            <input type="file" name="room_image" class="form-control" accept="image/*">
                            <small class="text-muted">Upload room photo (JPG, PNG recommended)</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection
