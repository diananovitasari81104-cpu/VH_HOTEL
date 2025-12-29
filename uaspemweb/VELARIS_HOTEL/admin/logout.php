<?php
session_start();
require_once '../config/database.php';
require_once '../config/functions.php';

// Log aktivitas sebelum logout
if (is_logged_in()) {
    log_activity('Logout from admin panel');
}

// Hapus semua session
session_unset();
session_destroy();

// Redirect ke login
header("Location: login.php");
exit;
?>