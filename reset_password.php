<?php
require_once 'config.php';
$msg = '';

// ── REQUEST RESET ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'request') {
    $email = $_POST['email'];
    $conn  = db_connect();

    $user  = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM users WHERE email='$email'"));

    if ($user) {
        $token = md5($user['username'] . time());
        mysqli_query($conn, "UPDATE users SET reset_token='$token' WHERE id={$user['id']}");

        $msg = "<div class='alert alert-success'>
                    Reset link (for demo — would be emailed in production):<br>
                    <a href='reset_password.php?token=$token&user={$user['id']}'>
                        reset_password.php?token=$token&user={$user['id']}
                    </a>
                </div>";
    } else {
        $msg = "<div class='alert alert-danger'>No account found with email: <b>$email</b></div>";
    }
    mysqli_close($conn);
}

// ── PERFORM RESET ─────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'reset') {
    $token    = $_POST['token'];
    $uid      = $_POST['user_id'];
    $new_pass = $_POST['new_password'];
    $conn     = db_connect();

    $user = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT * FROM users WHERE id=$uid AND reset_token='$token'"
    ));

    if ($user) {
        $hashed = md5($new_pass);
        mysqli_query($conn, "UPDATE users SET password='$hashed' WHERE id=$uid");
        $msg = "<div class='alert alert-success'>Password reset successful. <a href='index.php'>Login</a></div>";
    } else {
        $msg = "<div class='alert alert-danger'>Invalid or expired token.</div>";
    }
    mysqli_close($conn);
}

// Check if we have a token in GET (from the reset link)
$token_get = $_GET['token']   ?? '';
$uid_get   = $_GET['user']    ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg,#1a1a2e,#16213e,#0f3460); min-height:100vh; display:flex; align-items:center; }
        .reset-card { border-radius:16px; overflow:hidden; box-shadow:0 20px 60px rgba(0,0,0,.4); }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card reset-card">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-1"><i class="fas fa-key me-2"></i>Password Reset</h4>
                    <p class="text-muted mb-4">
                        <?php echo APP_NAME; ?>
                        <small class="text-muted">v<?php echo APP_VERSION; ?></small>
                    </p>

                    <?php echo $msg; ?>

                    <?php if (!$token_get): ?>
                    <form method="POST">
                        <input type="hidden" name="action" value="request">
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="text" name="email" class="form-control"
                                   value="<?php echo $_POST['email'] ?? ''; ?>"
                                   placeholder="Enter your account email">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                    </form>

                    <?php else: ?>
                    <form method="POST">
                        <input type="hidden" name="action"   value="reset">
                        <input type="hidden" name="token"    value="<?php echo $token_get; ?>">
                        <input type="hidden" name="user_id"  value="<?php echo $uid_get; ?>">
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control" required>
                            <small class="text-muted">Min length not enforced — any password accepted</small>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">Reset Password</button>
                    </form>
                    <?php endif; ?>

                    <div class="mt-3 text-center">
                        <a href="index.php" class="text-muted small">Back to login</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
