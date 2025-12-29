<?php
header('Content-Type: application/json');

require_once '../../config/database.php';
require_once '../../config/function.php';

// ===============================
// CEK LOGIN & ROLE CUSTOMER
// ===============================
if (!is_logged_in() || $_SESSION['role'] !== 'customer') {
    echo json_encode([
        'status' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

$id_user = $_SESSION['user_id'];

// ===============================
// GET DATA PROFILE
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $sql = "SELECT id_user, email, no_hp, role, created_at
            FROM users
            WHERE id_user = $id_user
            LIMIT 1";

    $user = fetch_single($sql);

    if ($user) {
        echo json_encode([
            'status' => true,
            'data' => $user
        ]);
    } else {
        echo json_encode([
            'status' => false,
            'message' => 'User tidak ditemukan'
        ]);
    }
    exit;
}

// ===============================
// UPDATE PROFILE
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $no_hp        = sanitize($_POST['no_hp'] ?? '');
    $password    = $_POST['password'] ?? '';
    $konfirmasi  = $_POST['konfirmasi_password'] ?? '';

    // Validasi no_hp
    if (empty($no_hp)) {
        echo json_encode([
            'status' => false,
            'message' => 'Nomor HP wajib diisi'
        ]);
        exit;
    }

    // ===============================
    // UPDATE TANPA PASSWORD
    // ===============================
    if (empty($password)) {

        $sql = "UPDATE users 
                SET no_hp = '$no_hp'
                WHERE id_user = $id_user";

        execute($sql);

        echo json_encode([
            'status' => true,
            'message' => 'Profil berhasil diperbarui'
        ]);
        exit;
    }

    // ===============================
    // UPDATE DENGAN PASSWORD
    // ===============================
    if ($password !== $konfirmasi) {
        echo json_encode([
            'status' => false,
            'message' => 'Konfirmasi password tidak cocok'
        ]);
        exit;
    }

    if (strlen($password) < 6) {
        echo json_encode([
            'status' => false,
            'message' => 'Password minimal 6 karakter'
        ]);
        exit;
    }

    $hash = hash_password($password);

    $sql = "UPDATE users 
            SET no_hp = '$no_hp', password = '$hash'
            WHERE id_user = $id_user";

    execute($sql);

    echo json_encode([
        'status' => true,
        'message' => 'Profil & password berhasil diperbarui'
    ]);
    exit;
}

// ===============================
// METHOD TIDAK VALID
// ===============================
echo json_encode([
    'status' => false,
    'message' => 'Invalid request method'
]);
exit;
