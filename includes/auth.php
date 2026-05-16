<?php
//        not whether the session is valid/not-expired/not-hijacked
function require_login() {
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

//        There is no server-side enforcement beyond this single function
function require_role($role) {
    require_login();
    if ($_SESSION['role'] !== $role) {
        echo "<div class='alert alert-danger'>Access denied. Required role: <b>$role</b>. Your role: <b>{$_SESSION['role']}</b></div>";
    }
}

// (a proper implementation would generate, store, and validate a CSRF token)
