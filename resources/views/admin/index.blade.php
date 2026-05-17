@extends('layouts.app')

@section('title', 'Admin Panel')

@section('content')
<h5 class="mb-4 fw-bold"><i class="fas fa-cog me-2"></i>Admin Panel</h5>

@if(session('ping_output'))
    <div class="alert alert-info">Ping command executed.</div>
@endif

<ul class="nav nav-tabs mb-4">
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'users' ? 'active' : '' }}" href="{{ route('admin.index', ['tab' => 'users']) }}">Users</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'logs' ? 'active' : '' }}" href="{{ route('admin.index', ['tab' => 'logs']) }}">Log Viewer</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'tools' ? 'active' : '' }}" href="{{ route('admin.index', ['tab' => 'tools']) }}">System Tools</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'backup' ? 'active' : '' }}" href="{{ route('admin.index', ['tab' => 'backup']) }}">Backup</a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ $tab === 'settings' ? 'active' : '' }}" href="{{ route('admin.index', ['tab' => 'settings']) }}">Settings</a>
    </li>
</ul>

@if($tab === 'users')
    <!-- USERS TAB -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <span class="fw-bold"><i class="fas fa-users me-2"></i>System Users</span>
                    <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Username</th>
                                <th>Full Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Last Login</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($users as $u)
                            <tr>
                                <td>{{ $u->id }}</td>
                                <td><strong>{{ $u->username }}</strong></td>
                                <td>{{ $u->full_name }}</td>
                                <td>{{ $u->email }}</td>
                                <td>
                                    @php
                                        $rc = ['admin'=>'danger','manager'=>'warning','staff'=>'primary'];
                                        $bc = $rc[$u->role] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $bc }}">{{ strtoupper($u->role) }}</span>
                                </td>
                                <td>{{ $u->last_login ? \Carbon\Carbon::parse($u->last_login)->diffForHumans() : 'Never' }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('admin.index', ['tab'=>'users', 'edit'=>$u->id]) }}" class="btn btn-outline-warning"><i class="fas fa-edit"></i></a>
                                        @if($u->username !== 'admin')
                                        <form action="{{ route('admin.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete user?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger"><i class="fas fa-trash"></i></button>
                                        </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold">DB Credentials</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th>Host</th><td>{{ env('DB_HOST') }}</td></tr>
                        <tr><th>User</th><td>{{ env('DB_USERNAME') }}</td></tr>
                        <tr><th>Database</th><td>{{ env('DB_DATABASE') }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('admin.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Add System User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="staff">Staff</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="isActive" checked>
                        <label class="form-check-label" for="isActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>

    @if(isset($edit_user))
    <!-- Edit User Modal -->
    <div class="modal fade show d-block" style="background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('admin.update', $edit_user) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Edit User: {{ $edit_user->username }}</h5>
                    <a href="{{ route('admin.index') }}" class="btn-close"></a>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" value="{{ $edit_user->username }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">New Password (leave blank to keep current)</label>
                        <input type="password" name="password" class="form-control" minlength="6">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="{{ $edit_user->full_name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="{{ $edit_user->email }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select" required>
                            <option value="staff" {{ $edit_user->role == 'staff' ? 'selected' : '' }}>Staff</option>
                            <option value="manager" {{ $edit_user->role == 'manager' ? 'selected' : '' }}>Manager</option>
                            <option value="admin" {{ $edit_user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" name="is_active" class="form-check-input" id="editIsActive" {{ $edit_user->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="editIsActive">Active</label>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ route('admin.index') }}" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-warning">Update User</button>
                </div>
            </form>
        </div>
    </div>
    @endif

@elseif($tab === 'logs')
    <!-- LOGS TAB -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-bold">Log Viewer</div>
        <div class="card-body">
            <form method="GET" class="d-flex gap-2 mb-3">
                <input type="hidden" name="tab" value="logs">
                <input type="text" name="log" class="form-control" placeholder="Log filename (e.g. laravel.log)"
                       value="{{ request('log', 'laravel.log') }}">
                <button type="submit" class="btn btn-outline-primary">View</button>
            </form>
            @if(isset($log_content))
            <pre class="bg-dark text-success p-3 rounded" style="max-height:400px;overflow:auto;font-size:.8rem;">{{ $log_content }}</pre>
            @endif
            <div class="alert alert-info mt-3">
                <strong>Security Info:</strong> Path traversal is now prevented. You can only view `.log` files inside the `storage/logs` directory.
            </div>
        </div>
    </div>

@elseif($tab === 'tools')
    <!-- TOOLS TAB -->
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold"><i class="fas fa-network-wired me-2"></i>Network Ping Tool</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.ping') }}">
                        @csrf
                        <div class="input-group mb-3">
                            <input type="text" name="ping_host" class="form-control"
                                   placeholder="e.g. 127.0.0.1" required>
                            <button type="submit" class="btn btn-primary">Ping</button>
                        </div>
                    </form>
                    @if(session('ping_output'))
                    <pre class="bg-dark text-light p-3 rounded" style="font-size:.8rem;max-height:300px;overflow:auto;">{{ session('ping_output') }}</pre>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white fw-bold"><i class="fas fa-server me-2"></i>System Information</div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr><th>PHP Version</th><td>{{ phpversion() }}</td></tr>
                        <tr><th>Laravel Version</th><td>{{ app()->version() }}</td></tr>
                        <tr><th>OS</th><td>{{ php_uname() }}</td></tr>
                        <tr><th>Document Root</th><td>{{ $_SERVER['DOCUMENT_ROOT'] ?? 'N/A' }}</td></tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

@elseif($tab === 'backup')
    <!-- BACKUP TAB -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-bold"><i class="fas fa-database me-2"></i>Database Backup</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.backup') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label">Backup Path</label>
                        <input type="text" name="backup_path" class="form-control"
                               value="/tmp/hotel_backup_{{ date('Ymd') }}.sql">
                        <small class="text-muted">Full path where backup will be saved on the server.</small>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-warning w-100"><i class="fas fa-save me-2"></i>Run Backup</button>
                    </div>
                </div>
            </form>
            @if(session('backup_output'))
            <pre class="mt-3 bg-dark text-light p-3 rounded" style="font-size:.8rem;">{{ session('backup_output') }}</pre>
            @endif
        </div>
    </div>

@elseif($tab === 'settings')
    <!-- SETTINGS TAB -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white fw-bold"><i class="fas fa-sliders-h me-2"></i>Application Settings</div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.settings') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Hotel Name</label>
                        <input type="text" name="hotel_name" class="form-control" value="{{ config('app.name') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Contact Email</label>
                        <input type="email" name="hotel_email" class="form-control" value="admin@grandpalace.com">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">Save Settings</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endif

@endsection
