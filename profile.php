<?php
require_once 'config.php';
require_once 'includes/auth.php';
require_login();

$page_title = 'My Profile';
$conn = db_connect();
$msg  = '';

// e.g. profile.php?id=1 lets any staff view admin's profile
$uid = isset($_GET['id']) ? $_GET['id'] : $_SESSION['user_id'];

// ── UPDATE PROFILE ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update') {
    $edit_uid  = $_POST['user_id'];
    $full_name = $_POST['full_name'];
    $email     = $_POST['email'];
    $phone     = $_POST['phone'];

    mysqli_query($conn, "UPDATE users SET full_name='$full_name',email='$email',phone='$phone' WHERE id=$edit_uid");
    $msg = "<div class='alert alert-success'>Profile updated.</div>";
    log_action("Profile updated for user $edit_uid");
}

// ── CHANGE PASSWORD ───────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'change_password') {
    $edit_uid    = $_POST['user_id'];
    $new_pass    = $_POST['new_password'];
    $confirm     = $_POST['confirm_password'];

    if ($new_pass === $confirm) {
        $hashed = md5($new_pass);
        mysqli_query($conn, "UPDATE users SET password='$hashed' WHERE id=$edit_uid");
        $msg = "<div class='alert alert-success'>Password changed.</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Passwords do not match.</div>";
    }
}

$user = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE id=$uid"));

require_once 'includes/header.php';
?>

<h5 class="mb-4 fw-bold"><i class="fas fa-user-circle me-2"></i>
    My Profile
    <?php if ($uid != $_SESSION['user_id']): ?>
    <span class="badge bg-warning">Viewing User ID: <?php echo $uid; ?></span>
    <?php endif; ?>
</h5>

<?php echo $msg; ?>

<?php if ($user): ?>
<div class="row g-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Profile Information</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action"  value="update">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" value="<?php echo $user['username']; ?>" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="full_name" class="form-control" value="<?php echo $user['full_name']; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" value="<?php echo $user['email']; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo $user['phone']; ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Role</label>
                        <input type="text" class="form-control" value="<?php echo $user['role']; ?>" disabled>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">Change Password</div>
            <div class="card-body">
                <form method="POST">
                    <input type="hidden" name="action"  value="change_password">
                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                    <div class="mb-3">
                        <label class="form-label">New Password</label>
                        <input type="password" name="new_password" class="form-control">
                        <small class="text-muted">Stored as MD5 (no salt)</small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control">
                    </div>
                    <button type="submit" class="btn btn-warning">Change Password</button>
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">Session Info</div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><th>Session ID</th><td><code><?php echo session_id(); ?></code></td></tr>
                    <tr><th>Login Time</th><td><?php echo date('Y-m-d H:i:s', $_SESSION['login_time'] ?? 0); ?></td></tr>
                    <tr><th>User Agent</th><td style="font-size:.75rem"><?php echo $_SERVER['HTTP_USER_AGENT'] ?? 'N/A'; ?></td></tr>
                    <tr><th>IP Address</th><td><?php echo $_SERVER['REMOTE_ADDR']; ?></td></tr>
                    <tr><th>Last Login</th><td><?php echo $user['last_login'] ?? 'N/A'; ?></td></tr>
                </table>
            </div>
        </div>
    </div>
</div>
<?php else: ?>
<div class="alert alert-danger">User with ID <strong><?php echo $uid; ?></strong> not found.</div>
<?php endif; ?>

<?php
mysqli_close($conn);
require_once 'includes/footer.php';
?>
