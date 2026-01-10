<?php
session_start();
require_once '../../config/database.php';
require_once '../../config/functions.php';

header('Content-Type: application/json');

$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

$user = fetch_single("SELECT * FROM users WHERE email='$email'");

if (!$user) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Akun tidak ditemukan'
    ]);
    exit;
}

if (!verify_password($password, $user['password'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Password salah'
    ]);
    exit;
}

// LOGIN BERHASIL
$_SESSION['customer_id']    = (int)$user['id_user'];
$_SESSION['customer_name']  = $user['nama_lengkap'] ?? 'Customer';
$_SESSION['customer_email'] = $user['email'] ?? '-';

echo json_encode([
    'status' => 'success',
    'name'   => $_SESSION['customer_name']
]);
exit;
