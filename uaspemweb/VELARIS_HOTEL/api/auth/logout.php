<?php
require_once '../../config/functions.php';

header('Content-Type: application/json');

// Hapus session
session_unset();
session_destroy();

echo json_encode([
    'status' => true,
    'message' => 'Logout berhasil'
]);
