<?php
require_once '../../config/database.php';
require_once '../../config/functions.php';

require_staff();

header('Content-Type: application/json');

$id = (int)($_POST['id'] ?? 0);
$action = $_POST['action'] ?? '';

// Ambil reservasi
$reservasi = fetch_single("SELECT * FROM reservasi WHERE id_reservasi = $id");

if (!$reservasi) {
    echo json_encode(['success' => false, 'message' => 'Reservation not found']);
    exit;
}

// Cek jika ini pengajuan pembatalan
if ($reservasi['status'] === 'pembatalan_diajukan') {

    $batal = fetch_single("SELECT * FROM pembatalan WHERE id_reservasi = $id");

    if (!$batal) {
        echo json_encode(['success' => false, 'message' => 'Cancellation request not found']);
        exit;
    }

    if ($action === 'approve') {
        // Setujui pembatalan
        execute("UPDATE reservasi SET status='batal' WHERE id_reservasi=$id");
        execute("UPDATE pembatalan SET status_pengajuan='disetujui', tgl_diproses=NOW() WHERE id_batal=".$batal['id_batal']);
        echo json_encode(['success' => true, 'message' => 'Pembatalan berhasil disetujui']);
        exit;
    } elseif ($action === 'reject') {
        // Tolak pembatalan
        execute("UPDATE reservasi SET status='lunas' WHERE id_reservasi=$id");
        execute("UPDATE pembatalan SET status_pengajuan='ditolak' WHERE id_batal=".$batal['id_batal']);
        echo json_encode(['success' => true, 'message' => 'Pembatalan berhasil ditolak']);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit;
    }

} else {
    echo json_encode(['success' => false, 'message' => 'Reservasi bukan pengajuan pembatalan']);
    exit;
}
