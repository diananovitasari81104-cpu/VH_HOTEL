<?php
header('Content-Type: application/json');

require_once '../../config/database.php';
require_once '../../config/function.php';

if (!is_logged_in() || $_SESSION['role'] !== 'customer') {
    echo json_encode(['status' => false, 'message' => 'Unauthorized']);
    exit;
}

$id_user      = $_SESSION['user_id'];
$id_reservasi = (int) ($_GET['id_reservasi'] ?? 0);

$data = fetch_single("
    SELECT p.bukti_pembayaran, p.status_verifikasi, p.tgl_upload
    FROM pembayaran p
    JOIN reservasi r ON p.id_reservasi = r.id_reservasi
    WHERE p.id_reservasi = $id_reservasi AND r.id_user = $id_user
");

if (!$data) {
    echo json_encode(['status' => false, 'message' => 'Data pembayaran belum tersedia']);
    exit;
}

echo json_encode([
    'status' => true,
    'data' => $data
]);
exit;
