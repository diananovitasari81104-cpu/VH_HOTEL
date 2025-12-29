<?php
header('Content-Type: application/json');

require_once '../../config/database.php';
require_once '../../config/function.php';

if (!is_logged_in() || $_SESSION['role'] !== 'customer') {
    echo json_encode(['status' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'message' => 'Invalid request']);
    exit;
}

$id_user      = $_SESSION['user_id'];
$id_reservasi = (int) ($_POST['id_reservasi'] ?? 0);
$alasan       = sanitize($_POST['alasan'] ?? '');

if (!$id_reservasi || !$alasan) {
    echo json_encode(['status' => false, 'message' => 'Data tidak lengkap']);
    exit;
}

// Pastikan reservasi milik user
$cek = fetch_single("
    SELECT id_reservasi 
    FROM reservasi 
    WHERE id_reservasi = $id_reservasi AND id_user = $id_user
");

if (!$cek) {
    echo json_encode(['status' => false, 'message' => 'Reservasi tidak valid']);
    exit;
}

// Insert pembatalan
insert("
    INSERT INTO pembatalan 
    (id_reservasi, tgl_pengajuan, alasan, status_pengajuan)
    VALUES ($id_reservasi, NOW(), '$alasan', 'pending')
");

echo json_encode([
    'status' => true,
    'message' => 'Permintaan pembatalan berhasil dikirim'
]);
exit;
