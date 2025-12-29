<?php
header('Content-Type: application/json');

require_once '../../config/database.php';
require_once '../../config/function.php';

if (!is_logged_in() || $_SESSION['role'] !== 'customer') {
    echo json_encode(['status' => false, 'message' => 'Unauthorized']);
    exit;
}

$id_user      = $_SESSION['user_id'];
$id_reservasi = (int) ($_GET['id'] ?? 0);

$reservasi = fetch_single("
    SELECT r.*, k.nama_kamar
    FROM reservasi r
    JOIN kamar k ON r.id_kamar = k.id_kamar
    WHERE r.id_reservasi = $id_reservasi AND r.id_user = $id_user
");

if (!$reservasi) {
    echo json_encode(['status' => false, 'message' => 'Data tidak ditemukan']);
    exit;
}

$experiences = fetch_all("
    SELECT e.nama_aktivitas, re.jumlah
    FROM reservasi_experiences re
    JOIN experiences e ON re.id_experience = e.id_experience
    WHERE re.id_reservasi = $id_reservasi
");

echo json_encode([
    'status' => true,
    'reservasi' => $reservasi,
    'experiences' => $experiences
]);
exit;
