<?php
header('Content-Type: application/json');

require_once '../../config/database.php';
require_once '../../config/function.php';

// ===============================
// CEK LOGIN CUSTOMER
// ===============================
if (!is_logged_in() || $_SESSION['role'] !== 'customer') {
    echo json_encode(['status' => false, 'message' => 'Unauthorized']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => false, 'message' => 'Invalid request']);
    exit;
}

$id_user        = $_SESSION['user_id'];
$id_kamar       = (int) ($_POST['id_kamar'] ?? 0);
$tgl_checkin    = $_POST['tgl_checkin'] ?? '';
$tgl_checkout   = $_POST['tgl_checkout'] ?? '';
$jumlah_kamar   = (int) ($_POST['jumlah_kamar'] ?? 1);
$experiences    = $_POST['experiences'] ?? []; // array id_experience => jumlah

// ===============================
// VALIDASI
// ===============================
if (!$id_kamar || !$tgl_checkin || !$tgl_checkout) {
    echo json_encode(['status' => false, 'message' => 'Data reservasi tidak lengkap']);
    exit;
}

// Ambil data kamar
$kamar = fetch_single("SELECT harga FROM kamar WHERE id_kamar = $id_kamar");
if (!$kamar) {
    echo json_encode(['status' => false, 'message' => 'Kamar tidak ditemukan']);
    exit;
}

$total_harga = $kamar['harga'] * $jumlah_kamar;

// ===============================
// HITUNG EXPERIENCE
// ===============================
foreach ($experiences as $id_exp => $qty) {
    $id_exp = (int)$id_exp;
    $qty    = (int)$qty;

    $exp = fetch_single("SELECT harga FROM experiences WHERE id_experience = $id_exp");
    if ($exp) {
        $total_harga += $exp['harga'] * $qty;
    }
}

// ===============================
// INSERT RESERVASI
// ===============================
$sql = "INSERT INTO reservasi 
        (id_user, id_kamar, tgl_checkin, tgl_checkout, jumlah_kamar, total_harga, status)
        VALUES 
        ($id_user, $id_kamar, '$tgl_checkin', '$tgl_checkout', $jumlah_kamar, $total_harga, 'pending')";

$id_reservasi = insert($sql);

if (!$id_reservasi) {
    echo json_encode(['status' => false, 'message' => 'Gagal membuat reservasi']);
    exit;
}

// ===============================
// INSERT EXPERIENCES
// ===============================
foreach ($experiences as $id_exp => $qty) {
    $id_exp = (int)$id_exp;
    $qty    = (int)$qty;

    if ($qty > 0) {
        insert("INSERT INTO reservasi_experiences 
                (id_reservasi, id_experience, jumlah)
                VALUES ($id_reservasi, $id_exp, $qty)");
    }
}

echo json_encode([
    'status' => true,
    'message' => 'Reservasi berhasil dibuat',
    'id_reservasi' => $id_reservasi
]);
exit;
