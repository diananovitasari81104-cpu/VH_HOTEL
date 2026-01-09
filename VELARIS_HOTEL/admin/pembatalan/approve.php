<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/functions.php';

header('Content-Type: application/json');

/**
 * CEK AKSES ADMIN / STAFF
 */
if (!is_staff()) {
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized'
    ]);
    exit;
}

/**
 * HANYA POST
 */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
    exit;
}

/**
 * VALIDASI ID PEMBATALAN
 */
$id_batal = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if (!$id_batal) {
    echo json_encode([
        'success' => false,
        'message' => 'ID pembatalan tidak valid'
    ]);
    exit;
}

/**
 * KONEKSI DB
 */
$db   = new Koneksi();
$conn = $db->getKoneksi();

/**
 * TRANSAKSI
 */
$conn->begin_transaction();

try {

    /**
     * 1. AMBIL DATA RESERVASI + KAMAR
     */
    $stmt = $conn->prepare("
        SELECT 
            b.id_reservasi,
            r.id_kamar,
            r.status
        FROM pembatalan b
        JOIN reservasi r ON r.id_reservasi = b.id_reservasi
        WHERE b.id_batal = ?
        FOR UPDATE
    ");
    $stmt->bind_param("i", $id_batal);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    if (!$data) {
        throw new Exception("Data pembatalan tidak ditemukan");
    }

    if ($data['status'] === 'batal') {
        throw new Exception("Reservasi sudah dibatalkan");
    }

    /**
     * 2. UPDATE STATUS PEMBATALAN
     */
    $stmt = $conn->prepare("
        UPDATE pembatalan
        SET status_pengajuan = 'disetujui',
            tgl_diproses = NOW()
        WHERE id_batal = ?
    ");
    $stmt->bind_param("i", $id_batal);
    $stmt->execute();

    /**
     * 3. UPDATE STATUS RESERVASI → BATAL
     */
    $stmt = $conn->prepare("
        UPDATE reservasi
        SET status = 'batal'
        WHERE id_reservasi = ?
    ");
    $stmt->bind_param("i", $data['id_reservasi']);
    $stmt->execute();

    /**
     * 4. KEMBALIKAN STOK KAMAR (OPSIONAL TAPI DISARANKAN)
     */
    $stmt = $conn->prepare("
        UPDATE kamar
        SET stok = stok + 1
        WHERE id_kamar = ?
    ");
    $stmt->bind_param("i", $data['id_kamar']);
    $stmt->execute();

    /**
     * COMMIT
     */
    $conn->commit();

    log_activity("Approve cancellation ID: $id_batal");

    echo json_encode([
        'success' => true,
        'message' => 'Pembatalan berhasil disetujui'
    ]);

} catch (Exception $e) {

    $conn->rollback();

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
