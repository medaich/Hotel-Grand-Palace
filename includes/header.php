<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') . ' | ' . APP_NAME : APP_NAME; ?></title>
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

<?php if (isset($_SESSION['user_id'])): ?>
<!-- Sidebar: Only visible when logged in -->
<div class="sidebar">
    <div class="brand">
        <h4><i class="fas fa-hotel me-2"></i><?php echo htmlspecialchars(APP_NAME, ENT_QUOTES, 'UTF-8'); ?></h4>
        <small>v<?php echo htmlspecialchars(APP_VERSION, ENT_QUOTES, 'UTF-8'); ?> &bull; Management System</small>
    </div>
    <nav class="nav flex-column pt-2">
        <span class="nav-section">Main</span>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
        </a>

        <span class="nav-section">Operations</span>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'rooms.php' ? 'active' : ''; ?>" href="rooms.php">
            <i class="fas fa-door-open me-2"></i>Rooms
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'bookings.php' ? 'active' : ''; ?>" href="bookings.php">
            <i class="fas fa-calendar-check me-2"></i>Bookings
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'guests.php' ? 'active' : ''; ?>" href="guests.php">
            <i class="fas fa-users me-2"></i>Guests
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : ''; ?>" href="services.php">
            <i class="fas fa-concierge-bell me-2"></i>Services
        </a>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'maintenance.php' ? 'active' : ''; ?>" href="maintenance.php">
            <i class="fas fa-tools me-2"></i>Maintenance
        </a>

        <span class="nav-section">Communication</span>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>" href="messages.php">
            <i class="fas fa-envelope me-2"></i>Messages
        </a>

        <span class="nav-section">Reports</span>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : ''; ?>" href="reports.php">
            <i class="fas fa-chart-bar me-2"></i>Reports
        </a>

        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <span class="nav-section">Administration</span>
        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin.php' ? 'active' : ''; ?>" href="admin.php">
            <i class="fas fa-cog me-2"></i>Admin Panel
        </a>
        <a class="nav-link" href="admin.php?action=phpinfo">
            <i class="fas fa-info-circle me-2"></i>System Info
        </a>
        <?php endif; ?>

        <span class="nav-section">Account</span>
        <a class="nav-link" href="profile.php">
            <i class="fas fa-user me-2"></i>My Profile
        </a>
        <a class="nav-link text-danger" href="logout.php">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
        </a>
    </nav>
</div>
<?php endif; ?>

<!-- Main Content wrapper starts here. If logged out, ms-0 clears the sidebar space -->
<div class="main-content <?php echo !isset($_SESSION['user_id']) ? 'ms-0' : ''; ?>">
    
    <?php if (isset($_SESSION['user_id'])): ?>
    <!-- Topbar: Only visible when logged in -->
    <div class="topbar">
        <div>
            <h6 class="mb-0 fw-bold"><?php echo isset($page_title) ? htmlspecialchars($page_title, ENT_QUOTES, 'UTF-8') : 'Dashboard'; ?></h6>
        </div>
        <div class="user-info d-flex align-items-center gap-3">
            <span><i class="fas fa-user-circle me-1"></i><?php echo htmlspecialchars($_SESSION['username'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?></span>
            <span class="badge badge-role-<?php echo htmlspecialchars($_SESSION['role'] ?? 'staff', ENT_QUOTES, 'UTF-8'); ?>">
                <?php echo strtoupper(htmlspecialchars($_SESSION['role'] ?? 'STAFF', ENT_QUOTES, 'UTF-8')); ?>
            </span>
            <span class="text-muted"><?php echo date('D, d M Y'); ?></span>
        </div>
    </div>
    <?php endif; ?>

    <!-- Inner content padding box. Your page content injects here. -->
    <div class="p-4">