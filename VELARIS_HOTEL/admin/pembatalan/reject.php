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

$id_batal = (int) $_POST['id'];
$catatan  = trim($_POST['catatan_admin']);

// ❗ CEK DATA MASIH PENDING
$data = fetch_single("
    SELECT 
        p.id_batal,
        p.id_reservasi,
        p.status_pengajuan,
        u.nama_lengkap
    FROM pembatalan p
    JOIN reservasi r ON p.id_reservasi = r.id_reservasi
    JOIN users u ON r.id_user = u.id_user
    WHERE p.id_batal = $id_batal
      AND p.status_pengajuan = 'pending'
");

if (!$data) {
    echo json_encode([
        'success' => false,
        'message' => 'Cancellation tidak ditemukan atau sudah diproses'
    ]);
    exit;
}

$conn->begin_transaction();

try {

    /**
     * 1. UPDATE STATUS PEMBATALAN → DITOLAK
     */
    $stmt = $conn->prepare("
        UPDATE pembatalan
        SET status_pengajuan = ?,
            tgl_diproses = NOW(),
            catatan_admin = ?
        WHERE id_batal = ?
    ");

    if (!$stmt) {
        throw new Exception("Prepare gagal (pembatalan)");
    }

    $status_pengajuan = 'ditolak';
    $stmt->bind_param("ssi", $status_pengajuan, $catatan, $id_batal);
    $stmt->execute();
    $stmt->close();


    /**
     * 2. UPDATE STATUS RESERVASI → LUNAS
     */
    $stmt = $conn->prepare("
        UPDATE reservasi
        SET status = ?
        WHERE id_reservasi = ?
    ");

    if (!$stmt) {
        throw new Exception("Prepare gagal (reservasi)");
    }

    $status_reservasi = 'lunas';
    $stmt->bind_param("si", $status_reservasi, $data['id_reservasi']);
    $stmt->execute();
    $stmt->close();


    /**
     * 3. COMMIT
     */
    $conn->commit();

    log_activity(
        "Rejected cancellation #{$id_batal} (Guest: {$data['nama_lengkap']})"
    );

    echo json_encode([
        'success' => true,
        'message' => 'Cancellation berhasil ditolak'
    ]);

} catch (Exception $e) {

    $conn->rollback();

    echo json_encode([
        'success' => false,
        'message' => 'Gagal memproses pembatalan'
    ]);
}
