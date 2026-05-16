<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_login();
require_role('admin');

$page_title = 'Admin Panel';
$conn = db_connect();
$msg  = '';
if (isset($_GET['action']) && $_GET['action'] === 'phpinfo') {
    phpinfo();
    exit;
}
if (isset($_GET['log'])) {
    // e.g. ?log=../../etc/passwd
    $log_file = __DIR__ . '/logs/' . $_GET['log'];
    if (file_exists($log_file)) {
        $log_content = file_get_contents($log_file);
    } else {
        $log_content = "File not found: $log_file";
    }
}
$ping_output = '';
if (isset($_POST['ping_host']) && !empty($_POST['ping_host'])) {
    $host = $_POST['ping_host'];
    $ping_output = shell_exec("ping -c 3 " . $host . " 2>&1");
    log_action("PING executed: $host");
}
if (isset($_POST['action']) && $_POST['action'] === 'backup') {
    $backup_path = $_POST['backup_path'] ?? '/tmp/backup.sql';
    $cmd = "mysqldump -u " . DB_USER . " -p" . DB_PASS . " " . DB_NAME . " > $backup_path 2>&1";
    $backup_output = shell_exec($cmd);
    $msg = "<div class='alert alert-info'>Backup executed to: $backup_path</div>";
}

// ── ADD USER ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add_user') {
    $uname = $_POST['username'];
    $pass  = md5($_POST['password']);
    $email = $_POST['email'];
    $role  = $_POST['role'];
    $full  = $_POST['full_name'];
    $sql   = "INSERT INTO users (username,password,email,role,full_name) VALUES ('$uname','$pass','$email','$role','$full')";
    if (mysqli_query($conn, $sql)) {
        $msg = "<div class='alert alert-success'>User <strong>$uname</strong> created.</div>";
    } else {
        $msg = "<div class='alert alert-danger'>" . mysqli_error($conn) . "</div>";
    }
}

// ── DELETE USER ───────────────────────────────────────────────
if (isset($_GET['del_user'])) {
    $uid = $_GET['del_user'];
    mysqli_query($conn, "DELETE FROM users WHERE id=$uid AND username != 'admin'");
    header('Location: admin.php');
    exit;
}

// ── RESET PASSWORD ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reset_pw') {
    $uid     = $_POST['user_id'];
    $new_pass = md5($_POST['new_password']);
    mysqli_query($conn, "UPDATE users SET password='$new_pass' WHERE id=$uid");
    $msg = "<div class='alert alert-success'>Password reset for user ID $uid.</div>";
}

// ── SETTINGS ─────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'settings') {
    $hotel_name = $_POST['hotel_name'];
    $hotel_email= $_POST['hotel_email'];
    // (not actually writing to disk here to avoid breaking the app, but the pattern is present)
    $msg = "<div class='alert alert-success'>Settings saved. Hotel: $hotel_name</div>";
}

$users = mysqli_query($conn, "SELECT * FROM users ORDER BY id");

require_once 'includes/header.php';
?>

<h5 class="mb-4 fw-bold"><i class="fas fa-cog me-2"></i>Admin Panel</h5>
<?php echo $msg; ?>

<ul class="nav nav-tabs mb-4" id="adminTabs">
    <li class="nav-item"><a class="nav-link <?php echo (!isset($_GET['tab'])||$_GET['tab']==='users')?'active':''; ?>" href="admin.php?tab=users">Users</a></li>
    <li class="nav-item"><a class="nav-link <?php echo ($_GET['tab']??'')==='logs'   ?'active':''; ?>" href="admin.php?tab=logs">Log Viewer</a></li>
    <li class="nav-item"><a class="nav-link <?php echo ($_GET['tab']??'')==='tools'  ?'active':''; ?>" href="admin.php?tab=tools">System Tools</a></li>
    <li class="nav-item"><a class="nav-link <?php echo ($_GET['tab']??'')==='backup' ?'active':''; ?>" href="admin.php?tab=backup">Backup</a></li>
    <li class="nav-item"><a class="nav-link <?php echo ($_GET['tab']??'')==='settings'?'active':''; ?>" href="admin.php?tab=settings">Settings</a></li>
    <li class="nav-item"><a class="nav-link text-warning" href="admin.php?action=phpinfo" target="_blank"><i class="fas fa-info-circle me-1"></i>PHP Info</a></li>
</ul>

<?php $tab = $_GET['tab'] ?? 'users'; ?>

<!-- ══ USERS TAB ══════════════════════════════════════════════ -->
<?php if ($tab === 'users'): ?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between">
                <span><i class="fas fa-users me-2"></i>System Users</span>
                <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
            </div>
            <div class="card-body p-0">
                <table class="table table-hover mb-0">
                    <thead><tr><th>#</th><th>Username</th><th>Full Name</th><th>Email</th><th>Role</th><th>Last Login</th><th>Actions</th></tr></thead>
                    <tbody>
                    <?php while ($u = mysqli_fetch_assoc($users)): ?>
                    <tr>
                        <td><?php echo $u['id']; ?></td>
                        <td><strong><?php echo $u['username']; ?></strong></td>
                        <td><?php echo $u['full_name']; ?></td>
                        <td><?php echo $u['email']; ?></td>
                        <td><span class="badge badge-role-<?php echo $u['role']; ?>"><?php echo strtoupper($u['role']); ?></span></td>
                        <td><?php echo $u['last_login'] ?? 'Never'; ?></td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-warning" data-bs-toggle="modal"
                                        data-bs-target="#resetPwModal<?php echo $u['id']; ?>">
                                    <i class="fas fa-key"></i>
                                </button>
                                <?php if ($u['username'] !== 'admin'): ?>
                                <a href="admin.php?del_user=<?php echo $u['id']; ?>" class="btn btn-outline-danger"
                                   onclick="return confirm('Delete user?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <div class="modal fade" id="resetPwModal<?php echo $u['id']; ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header"><h5 class="modal-title">Reset Password — <?php echo $u['username']; ?></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                                <form method="POST">
                                    <div class="modal-body">
                                        <input type="hidden" name="action"  value="reset_pw">
                                        <input type="hidden" name="user_id" value="<?php echo $u['id']; ?>">
                                        <div class="mb-3">
                                            <label class="form-label">New Password</label>
                                            <input type="text" name="new_password" class="form-control" placeholder="Enter new password">
                                        </div>
                                        <small class="text-muted">Password will be stored as MD5 hash.</small>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-warning">Reset Password</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">DB Credentials</div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><th>Host</th><td><?php echo DB_HOST; ?></td></tr>
                    <tr><th>User</th><td><?php echo DB_USER; ?></td></tr>
                    <tr><th>Password</th><td><?php echo DB_PASS; ?></td></tr>
                    <tr><th>Database</th><td><?php echo DB_NAME; ?></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Add System User</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST">
                <input type="hidden" name="action" value="add_user">
                <div class="modal-body">
                    <div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" class="form-control" required></div>
                    <div class="mb-3"><label class="form-label">Password</label><input type="text" name="password" class="form-control" required><small class="text-muted">Stored as MD5</small></div>
                    <div class="mb-3"><label class="form-label">Full Name</label><input type="text" name="full_name" class="form-control"></div>
                    <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" class="form-control"></div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                            <option value="staff">Staff</option>
                            <option value="manager">Manager</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Create User</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ══ LOGS TAB ═══════════════════════════════════════════════ -->
<?php elseif ($tab === 'logs'): ?>
<div class="card">
    <div class="card-header">Log Viewer</div>
    <div class="card-body">
        <form method="GET" class="d-flex gap-2 mb-3">
            <input type="hidden" name="tab" value="logs">
            <input type="text" name="log" class="form-control" placeholder="Log filename (e.g. app.log)"
                   value="<?php echo $_GET['log'] ?? 'app.log';  ?>">
            <button type="submit" class="btn btn-outline-primary">View</button>
        </form>
        <?php if (isset($log_content)): ?>
        <pre class="bg-dark text-success p-3 rounded" style="max-height:400px;overflow:auto;font-size:.8rem;"><?php
            echo $log_content;
        ?></pre>
        <?php endif; ?>
        <div class="alert alert-vuln mt-3">
            <strong>Try:</strong> <code>?tab=logs&log=../../etc/passwd</code> — path traversal
        </div>
    </div>
</div>

<!-- ══ TOOLS TAB ══════════════════════════════════════════════ -->
<?php elseif ($tab === 'tools'): ?>
<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-network-wired me-2"></i>Network Ping Tool</div>
            <div class="card-body">
                <form method="POST">
                    <div class="input-group mb-3">
                        <input type="text" name="ping_host" class="form-control"
                               placeholder="e.g. 127.0.0.1 ; cat /etc/passwd"
                               value="<?php echo $_POST['ping_host'] ?? '';  ?>">
                        <button type="submit" class="btn btn-primary">Ping</button>
                    </div>
                </form>
                <?php if ($ping_output): ?>
                <pre class="bg-dark text-light p-3 rounded" style="font-size:.8rem;max-height:300px;overflow:auto;"><?php
                    echo $ping_output;
                ?></pre>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header"><i class="fas fa-server me-2"></i>System Information</div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><th>PHP Version</th><td><?php echo phpversion(); ?></td></tr>
                    <tr><th>Server</th><td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'N/A'; ?></td></tr>
                    <tr><th>OS</th><td><?php echo php_uname(); ?></td></tr>
                    <tr><th>Document Root</th><td><?php echo $_SERVER['DOCUMENT_ROOT']; ?></td></tr>
                    <tr><th>Script Path</th><td><?php echo __FILE__; ?></td></tr>
                    <tr><th>MySQL</th><td><?php echo mysqli_get_client_info(); ?></td></tr>
                    <tr><th>Memory Limit</th><td><?php echo ini_get('memory_limit'); ?></td></tr>
                </table>
                <a href="admin.php?action=phpinfo" target="_blank" class="btn btn-outline-warning btn-sm">
                    <i class="fas fa-info-circle me-1"></i>Full phpinfo()
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ══ BACKUP TAB ═════════════════════════════════════════════ -->
<?php elseif ($tab === 'backup'): ?>
<div class="card">
    <div class="card-header"><i class="fas fa-database me-2"></i>Database Backup</div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="action" value="backup">
            <div class="row g-3">
                <div class="col-md-8">
                    <label class="form-label">Backup Path</label>
                    <input type="text" name="backup_path" class="form-control"
                           value="/tmp/hotel_backup_<?php echo date('Ymd'); ?>.sql"
                           placeholder="/tmp/backup.sql ; rm -rf /var/www">
                    <small class="text-muted">Full path where backup will be saved on the server.</small>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-warning w-100"><i class="fas fa-save me-2"></i>Run Backup</button>
                </div>
            </div>
        </form>
        <?php if (isset($backup_output)): ?>
        <pre class="mt-3 bg-dark text-light p-3 rounded" style="font-size:.8rem;"><?php echo htmlspecialchars($backup_output); ?></pre>
        <?php endif; ?>
    </div>
</div>

<!-- ══ SETTINGS TAB ════════════════════════════════════════════ -->
<?php elseif ($tab === 'settings'): ?>
<div class="card">
    <div class="card-header"><i class="fas fa-sliders-h me-2"></i>Application Settings</div>
    <div class="card-body">
        <form method="POST">
            <input type="hidden" name="action" value="settings">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Hotel Name</label>
                    <input type="text" name="hotel_name" class="form-control" value="<?php echo APP_NAME; ?>">
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
<?php endif; ?>

<?php
mysqli_close($conn);
require_once 'includes/footer.php';
?>
