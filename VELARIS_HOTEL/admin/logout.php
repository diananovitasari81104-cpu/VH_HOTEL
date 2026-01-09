<?php
session_start();
require_once '../config/functions.php';

/* LOG ACTIVITY JIKA ADMIN LOGIN */
if (function_exists('is_logged_in') && is_logged_in()) {
    log_activity('Admin logout');
}

/* HAPUS SEMUA DATA SESSION */
$_SESSION = [];

/* HAPUS COOKIE SESSION */
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

/* DESTROY SESSION */
session_destroy();

/* REDIRECT + FEEDBACK */
header("Location: login.php?logout=success");
exit;
