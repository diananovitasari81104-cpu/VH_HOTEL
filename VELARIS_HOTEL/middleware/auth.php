<?php
require_once __DIR__ . '/../config/functions.php';

if (!is_logged_in()) {
    header("Location: ../auth/login.php");
    exit;
}
