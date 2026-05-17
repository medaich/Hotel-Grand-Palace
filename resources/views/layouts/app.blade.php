<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') | {{ config('app.name', 'Hotel Grand Palace') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root { --sidebar-width: 260px; }
        body { font-family: 'Segoe UI', sans-serif; background: #f4f6f9; }
        .sidebar {
            width: var(--sidebar-width); height: 100vh;
            background: linear-gradient(180deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            position: fixed; top: 0; left: 0; z-index: 100; padding-top: 0;
            overflow-y: auto; padding-bottom: 20px;
        }
        .sidebar .brand { padding: 20px; background: rgba(255,255,255,.05); border-bottom: 1px solid rgba(255,255,255,.1); }
        .sidebar .brand h4 { color: #e2b96f; margin: 0; font-size: 1rem; font-weight: 700; }
        .sidebar .brand small { color: rgba(255,255,255,.5); font-size: .7rem; }
        .sidebar .nav-link { color: rgba(255,255,255,.75); padding: 10px 20px; border-radius: 0; transition: all .2s; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #fff; background: rgba(255,255,255,.1); border-left: 3px solid #e2b96f; }
        .sidebar .nav-link i { width: 20px; }
        .sidebar .nav-section { color: rgba(255,255,255,.35); font-size: .7rem; text-transform: uppercase; letter-spacing: 1px; padding: 12px 20px 4px; }
        .main-content { margin-left: var(--sidebar-width); min-height: 100vh; }
        .topbar { background: #fff; border-bottom: 1px solid #e0e0e0; padding: 12px 24px; display: flex; align-items: center; justify-content: space-between; }
        .topbar .user-info { font-size: .85rem; color: #555; }
        .card { border: none; box-shadow: 0 2px 8px rgba(0,0,0,.08); border-radius: 10px; }
        .card-header { background: #fff; border-bottom: 1px solid #f0f0f0; font-weight: 600; }
        .stat-card { border-radius: 12px; color: #fff; padding: 20px; }
        .badge-role-admin   { background: #dc3545; }
        .badge-role-manager { background: #fd7e14; }
        .badge-role-staff   { background: #0d6efd; }
        .table thead th { background: #f8f9fa; font-weight: 600; font-size: .85rem; color: #495057; }
        .status-available   { color: #198754; font-weight: 600; }
        .status-occupied    { color: #dc3545; font-weight: 600; }
        .status-maintenance { color: #fd7e14; font-weight: 600; }
        .alert-vuln { background: #fff3cd; border-left: 4px solid #ffc107; font-size: .8rem; padding: 8px 12px; }
    </style>
</head>
<body>

@auth
<div class="sidebar">
    <div class="brand">
        <h4><i class="fas fa-hotel me-2"></i>{{ config('app.name', 'Hotel Grand Palace') }}</h4>
        <small>Management System</small>
    </div>
    <nav class="nav flex-column pt-2">
        <span class="nav-section">Main</span>
        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </a>

        <span class="nav-section">Operations</span>
        <a class="nav-link {{ request()->routeIs('rooms.*') ? 'active' : '' }}" href="{{ route('rooms.index') }}">
            <i class="fas fa-door-open me-2"></i>Rooms
        </a>
        <a class="nav-link {{ request()->routeIs('bookings.*') ? 'active' : '' }}" href="{{ route('bookings.index') }}">
            <i class="fas fa-calendar-check me-2"></i>Bookings
        </a>
        <a class="nav-link {{ request()->routeIs('guests.*') ? 'active' : '' }}" href="{{ route('guests.index') }}">
            <i class="fas fa-users me-2"></i>Guests
        </a>
        <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
            <i class="fas fa-concierge-bell me-2"></i>Services
        </a>
        <a class="nav-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}" href="{{ route('maintenance.index') }}">
            <i class="fas fa-tools me-2"></i>Maintenance
        </a>

        <span class="nav-section">Communication</span>
        <a class="nav-link {{ request()->routeIs('messages.*') ? 'active' : '' }}" href="{{ route('messages.index') }}">
            <i class="fas fa-envelope me-2"></i>Messages
        </a>

        <span class="nav-section">Reports</span>
        <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}">
            <i class="fas fa-chart-bar me-2"></i>Reports
        </a>

        @if(auth()->user()->role === 'admin')
        <span class="nav-section">Administration</span>
        <a class="nav-link {{ request()->routeIs('admin.*') ? 'active' : '' }}" href="{{ route('admin.index') }}">
            <i class="fas fa-cog me-2"></i>Admin Panel
        </a>
        <a class="nav-link {{ request()->routeIs('activity_logs.*') ? 'active' : '' }}" href="{{ route('activity_logs.index') }}">
            <i class="fas fa-history me-2"></i>Activity Logs
        </a>
        @endif

        <span class="nav-section">Account</span>
        <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.index') }}">
            <i class="fas fa-user me-2"></i>My Profile
        </a>
        <form method="POST" action="{{ route('logout') }}" id="logout-form" style="display: none;">
            @csrf
        </form>
        <a class="nav-link text-danger" href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
        </a>
    </nav>
</div>
@endauth

<div class="main-content {{ !auth()->check() ? 'ms-0' : '' }}">
    @auth
    <div class="topbar">
        <div>
            <h6 class="mb-0 fw-bold">@yield('title')</h6>
        </div>
        <div class="user-info d-flex align-items-center gap-3">
            <span><i class="fas fa-user-circle me-1"></i>{{ auth()->user()->username }}</span>
            <span class="badge badge-role-{{ auth()->user()->role }}">
                {{ strtoupper(auth()->user()->role) }}
            </span>
            <span class="text-muted">{{ date('D, d M Y') }}</span>
        </div>
    </div>
    @endauth

    <div class="p-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</body>
</html>
