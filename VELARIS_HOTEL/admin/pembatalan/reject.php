<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/functions.php';

header('Content-Type: application/json');

// ❗ AJAX TIDAK BOLEH REDIRECT
if (!is_staff()) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

// ❗ HARUS POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
    exit;
}

// ❗ VALIDASI INPUT
if (empty($_POST['id']) || empty($_POST['catatan_admin'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Data tidak lengkap'
    ]);
    exit;
}

$id = (int) $_POST['id'];
$catatan = escape(trim($_POST['catatan_admin']));

// ❗ CEK DATA MASIH PENDING
$cancel = fetch_single("
    SELECT p.*, u.nama_lengkap
    FROM pembatalan p
    JOIN reservasi r ON p.id_reservasi = r.id_reservasi
    JOIN users u ON r.id_user = u.id_user
    WHERE p.id_batal = $id
      AND p.status_pengajuan = 'pending'
");

if (!$cancel) {
    echo json_encode([
        'success' => false,
        'message' => 'Cancellation tidak ditemukan atau sudah diproses'
    ]);
    exit;
}

// ❗ UPDATE STATUS
$now = date('Y-m-d H:i:s');

$sql = "
    UPDATE pembatalan SET
        status_pengajuan = 'ditolak',
        tgl_diproses = '$now',
        catatan_admin = '$catatan'
    WHERE id_batal = $id
";

if (execute($sql)) {
    log_activity("Rejected cancellation #$id (Guest: {$cancel['nama_lengkap']})");

    echo json_encode([
        'success' => true,
        'message' => 'Cancellation rejected'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Gagal menolak pembatalan'
    ]);
}
