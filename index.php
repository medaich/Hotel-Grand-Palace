<?php
require_once 'config.php';

// Already logged in — send straight to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error   = '';
$success = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';

    $conn  = db_connect();
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '" . MD5($password) . "' AND is_active = 1";

    log_action("LOGIN ATTEMPT: $query");

    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);

        $_SESSION['user_id']   = $user['id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['role']      = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['email']     = $user['email'];
        $_SESSION['login_time'] = time();

        // Update last login (no injection risk here but still raw)
        mysqli_query($conn, "UPDATE users SET last_login = NOW() WHERE id = {$user['id']}");

        $redirect = $_GET['redirect'] ?? 'dashboard.php';
        header("Location: $redirect");
        exit;
    } else {
        $error = "Invalid credentials for user: <b>$username</b>";
    }
    mysqli_close($conn);
}

$msg = $_GET['msg'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | <?php echo APP_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460); min-height: 100vh; display: flex; align-items: center; }
        .login-card { border-radius: 16px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,.4); }
        .login-left  { background: linear-gradient(180deg,#0f3460,#533483); padding: 40px; color: #fff; }
        .login-right { background: #fff; padding: 40px; }
        .login-left h2 { color: #e2b96f; font-weight: 700; }
        .brand-icon  { font-size: 3rem; color: #e2b96f; margin-bottom: 16px; }
        .btn-login   { background: linear-gradient(135deg,#0f3460,#533483); border: none; }
        .hint-box    { background: #f8f9fa; border-radius: 8px; padding: 12px; font-size: .8rem; }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-9">
            <div class="card login-card">
                <div class="row g-0">
                    <div class="col-md-5 login-left d-flex flex-column justify-content-center">
                        <div class="text-center">
                            <div class="brand-icon"><i class="fas fa-hotel"></i></div>
                            <h2><?php echo APP_NAME; ?></h2>
                            <p class="opacity-75">Hotel Management System</p>
                            <hr class="border-light opacity-25">
                            <small class="opacity-50">Version <?php echo APP_VERSION; ?></small>
                        </div>
                    </div>
                    <div class="col-md-7 login-right">
                        <h4 class="fw-bold mb-1">Welcome Back</h4>
                        <p class="text-muted mb-4">Sign in to your account</p>

                        <?php if ($msg): ?>
                            <div class="alert alert-info"><?php echo $msg; ?></div>
                        <?php endif; ?>

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST" action="index.php<?php echo isset($_GET['redirect']) ? '?redirect='.$_GET['redirect'] : ''; ?>">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    <input type="text" name="username" class="form-control"
                                           value="<?php echo $_POST['username'] ?? ''; ?>"
                                           placeholder="Enter username">
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label fw-semibold">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                                    <input type="password" name="password" class="form-control" placeholder="Enter password">
                                </div>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="remember_me" class="form-check-input" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">Remember me</label>
                            </div>
                            <button type="submit" class="btn btn-login btn-primary w-100 py-2">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>
                        </form>

                        <hr class="my-4">
                        <div class="hint-box">
                            <strong><i class="fas fa-key me-1"></i>Demo Credentials:</strong><br>
                            admin / admin123 &nbsp;|&nbsp; manager / manager123 &nbsp;|&nbsp; staff / staff123
                        </div>

                        <div class="mt-3 text-center">
                            <a href="reset_password.php" class="text-muted small">Forgot password?</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
if (isset($_POST['remember_me']) && isset($_POST['username'])) {
    setcookie('remembered_user', $_POST['username'], time() + (86400 * 30), '/');
}
?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
