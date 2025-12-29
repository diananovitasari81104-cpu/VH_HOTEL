<?php
require_once __DIR__ . '/../config/function.php';

if (!is_logged_in()) {
    header("Location: ../auth/login.php");
    exit;
}
